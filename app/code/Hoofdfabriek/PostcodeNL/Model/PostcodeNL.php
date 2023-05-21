<?php
namespace Hoofdfabriek\PostcodeNL\Model;

use Magento\Framework\HTTP\Adapter\CurlFactory;
use Hoofdfabriek\PostcodeNL\Model\Config;

/**
 * Class PostcodeNL
 */
class PostcodeNL
{
    const API_TIMEOUT = 3;
    const API_URL = 'https://api.postcode.nl/';
    const API_EU_URL = 'https://api.postcode.eu/';

    /**
     * @var null|string
     */
    private $httpResponseRaw = null;

    /**
     * @var null|string
     */
    private $httpResponseCode = null;

    /**
     * @var null|string
     */
    private $httpResponseCodeClass = null;

    /**
     * @var null|string
     */
    private $httpClientError = null;

    /**
     * Use only for development purpose
     *
     * @var bool
     */
    private $debugMode = false;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CurlFactory
     */
    private $curlFactory;

    /**
     * PostcodeNL constructor.
     *
     * @param \Hoofdfabriek\PostcodeNL\Model\Config $config
     * @param CurlFactory $curlFactory
     */
    public function __construct(
        Config $config,
        CurlFactory $curlFactory
    ) {
        $this->config = $config;
        $this->curlFactory = $curlFactory;
    }


    /**
     * Check if postcodenl configuration is ready
     *
     * @return array|bool
     */
    public function checkApiReady()
    {
        if (!$this->config->isPostcodeNLEnabled()) {
            return ['message' => __('PostcodeNL API is not enabled.')];
        }

        if ($this->config->getApiKey() === '' || $this->config->getApiSecret() === '') {
            return [
                'message' => __('Postcode.nl API is not configured.'),
                'info' => [__('Configure your `API key` and `API secret`.')]
            ];
        }

        $curlVersion = curl_version();

        if (!($curlVersion['features'] & CURL_VERSION_SSL)) {
            return ['message' => __('Cannot connect to Postcode.nl API: Server is missing SSL support for CURL')];
        }
        return true;
    }

    /**
     * Get address information from postcodenl
     *
     * @param string $postcode
     * @param string $houseNumber
     * @param string $houseNumberAddition
     * @return array|bool
     */
    public function lookupAddress($postcode, $houseNumber, $houseNumberAddition = '')
    {
        $message = $this->checkApiReady();
        if ($message !== true) {
            return $message;
        }

        // Some basic user data 'fixing', remove any not-letter, not-number characters
        $postcode = preg_replace('~[^a-z0-9]~i', '', $postcode);
        // Basic postcode format checking
        if (!preg_match('~^[1-9][0-9]{3}[a-z]{2}$~i', $postcode)) {
            $response['message'] = __('Invalid postcode format, use `1234AB` format.');
            return $response;
        }

        if ($houseNumberAddition == '')
        {
            // If people put the housenumber addition in the housenumber field - split this.
            list($houseNumber, $houseNumberAddition) = $this->_splitHouseNumber($houseNumber);
        }

        return $this->_lookupAddress($postcode, $houseNumber, $houseNumberAddition);
    }

    /**
     * Split a housenumber addition from a housenumber.
     *
     * Examples: "123 2", "123 rood", "123a", "123a4", "123-a", "123 II"
     * (the official notation is to separate the housenumber and addition with a single space)
     *
     * @param string $houseNumber
     * @return array Array with houseNumber and houseNumberAddition values
     */
    protected function _splitHouseNumber($houseNumber)
    {
        $houseNumberAddition = '';
        if (preg_match(
            '~^(?<number>[0-9]+)(?:[^0-9a-zA-Z]+(?<addition1>[0-9a-zA-Z ]+)|(?<addition2>[a-zA-Z](?:[0-9a-zA-Z ]*)))?$~',
            $houseNumber, $match)
        ) {
            $houseNumber = $match['number'];
            $houseNumberAddition = isset($match['addition2']) ? $match['addition2'] : (isset($match['addition1']) ? $match['addition1'] : '');
        }
        return [$houseNumber, $houseNumberAddition];
    }

    /**
     * Make API request
     *
     * @param string $url
     * @return array
     */
    private function _callApi($url)
    {
        $curl = $this->curlFactory->create();
        $curl->addOption(CURLOPT_CONNECTTIMEOUT, self::API_TIMEOUT);
        $curl->addOption(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $curl->addOption(CURLOPT_USERPWD, $this->config->getApiKey() .':'. $this->config->getApiSecret());
        $curl->addOption(CURLOPT_USERAGENT, $this->_getUserAgent());
        $curl->write(\Zend_Http_Client::GET, $url);

        $responce = \Zend_Http_Response::fromString($curl->read());
        $this->httpResponseRaw = $responce->getBody();
        $this->httpResponseCode = $curl->getInfo(CURLINFO_HTTP_CODE);
        $this->httpResponseCodeClass = (int)floor($this->httpResponseCode / 100) * 100;
        $this->httpClientError = $curl->getErrno() ? sprintf('cURL error: %s', $curl->getErrno()) : null;
        $curl->close();
        return json_decode($this->httpResponseRaw, true);
    }

    /**
     * @return string
     */
    private function _getUserAgent()
    {
        return 'PostcodeNl_Api_MagentoPlugin/';
    }

    /**
     *API info - https://api.postcode.nl/documentation/nl/v1/Address/viewByPostcode
     *
     * @param string $postcode
     * @param string $houseNumber
     * @param string $houseNumberAddition
     *
     * @return array
     */
    private function _lookupAddress($postcode, $houseNumber, $houseNumberAddition)
    {
        $response = [];
        try {
            $url = self::API_EU_URL . 'nl/v1/addresses/postcode/' .
                rawurlencode($postcode). '/'.
                rawurlencode($houseNumber) . '/'.
                rawurlencode($houseNumberAddition);

            $jsonData = $this->_callApi($url);

            if ($this->debugMode) {
                $response['debugInfo'] = $this->buildDebugInfo($jsonData);
            }

            if ($this->httpResponseCode == 200 && is_array($jsonData) && isset($jsonData['postcode'])) {
                return array_merge($response, $jsonData);
            }

            if (isset($jsonData['exceptionId'])) {
                if ($this->httpResponseCode == 400 || $this->httpResponseCode == 404) {
                    $response['message'] = $this->mapErrorMessage($jsonData['exceptionId']);
                    return $response;
                }
            }
            return array_merge($response, $this->errorResponse());
        } catch (\Exception $e) {
            return array_merge($response, $this->errorResponse());
        }
    }

    /**
     * Wrap API request
     *
     * @param string $url
     * @param string $errorMessage
     * @return array
     */
    private function _wrapRequest($url, $errorMessage = '')
    {
        if (!$errorMessage) {
            $errorMessage = __('Something went wrong, please try later.');
        }

        $response = [];
        try {
            $jsonData = $this->_callApi($url);

            if ($this->debugMode) {
                $response['debugInfo'] = $this->buildDebugInfo($jsonData);
            }

            if ($this->httpResponseCode == 200 && is_array($jsonData)) {
                return array_merge($response, $jsonData);
            }

            if ($this->httpResponseCode == 400 || $this->httpResponseCode == 404) {
                $response['message'] = $errorMessage;
            }
        } catch (\Exception $e) {
            return array_merge($response, ['message' => $errorMessage]);
        }

        return $response;
    }

    /**
     * API info - https://api.postcode.nl/documentation/account/v1/Account/getInfo
     *
     * @return array|bool
     */
    public function getAccountInfo()
    {
        $message = $this->checkApiReady();

        if ($message !== true) {
            return false;
        }

        $url = self::API_EU_URL . 'account/v1/info';

        return $this->_wrapRequest($url, __('Something went wrong. Can not validate API creds.'));
    }

    /**
     * Belgium post areas autocomplete - https://api.postcode.nl/documentation/be/v1/AutoComplete/completePostalArea
     *
     * @param string $area
     * @return array|bool
     */
    public function belgiumPostAreaAutocomplete($area)
    {
        $message = $this->checkApiReady();
        if ($message !== true) {
            return $message;
        }

        $url = self::API_EU_URL . 'be/v1/autocomplete/postal-area/' . rawurlencode($area);

        $items = $this->_wrapRequest($url, __('Something went wrong. Can not get postal area.'));
        //format data
        foreach ($items as $key => $item) {
            if (isset($item['municipalityName'])) {
                $items[$key]['value'] = $items[$key]['label'] = $item['municipalityName'];
                if (isset($item['postcode'])) {
                    $items[$key]['value'] = $items[$key]['label'] = $item['postcode'] . ' ' . $item['municipalityName'];
                }
                if (isset($item['matchedName'])) {
                    $items[$key]['label'] .= " ({$item['matchedName']})";
                }
            }
        }

        return $items;
    }

    /**
     * Belguim house Number autocomplete - https://api.postcode.nl/documentation/be/v1/AutoComplete/completeHouseNumber
     *
     * @param string $area
     * @return array|bool
     */
    public function belgiumHouseNumberAutocomplete($streetId, $postcode, $houseNumber, $validation = 'none', $language = 'nl')
    {
        $message = $this->checkApiReady();
        if ($message !== true) {
            return $message;
        }
        $url = self::API_EU_URL . 'be/v1/autocomplete/house-number/' .
            rawurlencode($streetId) .'/'.
            rawurlencode($postcode) .'/'.
            rawurlencode($language) .'/'.
            rawurlencode($validation) .'/'.
            rawurlencode($houseNumber);

        $items = $this->_wrapRequest($url, __('Something went wrong. Can not get housenumber.'));

        //format data
        foreach ($items as $key => $item) {
            if (isset($item['houseNumber'])) {
                $items[$key]['value'] = $items[$key]['label'] = $item['houseNumber'];
            }
        }
        return $items;
    }

    /**
     * Belgium street autocomplete - https://api.postcode.nl/documentation/be/v1/AutoComplete/completeStreet
     *
     * @param string $municipalityNisCode
     * @param string $streetName
     * @param null|string $postcode
     * @return array|bool
     */
    public function belgiumStreetAutocomplete($municipalityNisCode, $streetName, $postcode = null)
    {
        $message = $this->checkApiReady();
        if ($message !== true) {
            return $message;
        }

        $url = self::API_EU_URL . 'be/v1/autocomplete/street/' .
            rawurlencode($municipalityNisCode) . '/';

        $url .= $postcode !== null ? rawurlencode($postcode)  : '';
        $url .= '/' . rawurlencode($streetName);

        $items = $this->_wrapRequest($url, __('Something went wrong. Can not get street.'));

        //format data
        foreach ($items as $key => $item) {
            if (isset($item['streetName'])) {
                $items[$key]['value'] = $items[$key]['label'] = $item['streetName'];
            }
        }
        return $items;
    }

    /**
     * @param string $code
     * @return \Magento\Framework\Phrase
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function mapErrorMessage($code)
    {
        $response = __('Incorrect address.');

        switch ($code) {
            case 'PostcodeNl_Controller_Address_PostcodeTooShortException':
            case 'PostcodeNl_Controller_Address_PostcodeTooLongException':
            case 'PostcodeNl_Controller_Address_NoPostcodeSpecifiedException':
            case 'PostcodeNl_Controller_Address_InvalidPostcodeException':
                $response = __('Invalid postcode format, use `1234AB` format.');
                break;
            case 'PostcodeNl_Service_PostcodeAddress_AddressNotFoundException':
                $response = __('Zip/Postal Code + housenumber combination does not seem to exist.');
                break;
            case 'PostcodeNl_Controller_Address_InvalidHouseNumberException':
            case 'PostcodeNl_Controller_Address_NoHouseNumberSpecifiedException':
            case 'PostcodeNl_Controller_Address_NegativeHouseNumberException':
            case 'PostcodeNl_Controller_Address_HouseNumberTooLargeException':
            case 'PostcodeNl_Controller_Address_HouseNumberIsNotAnIntegerException':
                $response = __('Housenumber format is not valid.');
        }

        return $response;
    }

    /**
     * @return array
     */
    private function errorResponse()
    {
        return [
            'message' => __('Validation error, please use manual input.')
        ];
    }

    /**
     * Debug information
     *
     * @param $jsonData
     * @return array
     */
    private function buildDebugInfo($jsonData)
    {
        return [
            'rawResponse' => $this->httpResponseRaw,
            'responseCode' => $this->httpResponseCode,
            'responseCodeClass' => $this->httpResponseCodeClass,
            'parsedResponse' => $jsonData,
            'httpClientError' => $this->httpClientError,
            'configuration' => [
                'key' => $this->config->getApiKey(),
                'secret' => substr($this->config->getApiSecret(), 0, 6) .'[hidden]',
                'debug' => $this->debugMode,
            ]
        ];
    }
}
