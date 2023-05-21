<?php
/**
 * ||GEISSWEB| EU VAT Enhanced
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GEISSWEB End User License Agreement
 * that is available through the world-wide-web at this URL: https://www.geissweb.de/legal-information/eula
 *
 * DISCLAIMER
 *
 * Do not edit this file if you wish to update the extension in the future. If you wish to customize the extension
 * for your needs please refer to our support for more information.
 *
 * @copyright   Copyright (c) 2015 GEISS Weblösungen (https://www.geissweb.de)
 * @license     https://www.geissweb.de/legal-information/eula GEISSWEB End User License Agreement
 */

namespace Geissweb\Euvat\Model\Validation;

use Geissweb\Euvat\Api\Data\ValidationResultInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class Result
 * Implementation for ValidationResultInterface
 */
class Result extends AbstractExtensibleModel implements ValidationResultInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'vat_validation';

    /**
     * @var string
     */
    protected $_eventObject = 'result';

    /**
     * Get warning
     *
     * @inheridoc
     * @return string
     */
    public function getWarning() : ?string
    {
        return $this->getData(self::KEY_WARNING);
    }

    /**
     * Set warning
     *
     * @param bool $bool
     * @return $this
     */
    public function setWarning(bool $bool) : ValidationResultInterface
    {
        return $this->setData(self::KEY_WARNING, $bool);
    }

    /**
     * Get error
     *
     * @return string
     */
    public function getError() : ?string
    {
        return $this->getData(self::KEY_ERROR);
    }

    /**
     * Set error
     *
     * @param bool $bool
     * @return $this
     */
    public function setError(bool $bool) : ValidationResultInterface
    {
        return $this->setData(self::KEY_ERROR, $bool);
    }

    /**
     * Get request message
     *
     * @return string
     */
    public function getRequestMessage() : ?string
    {
        return $this->getData(self::KEY_REQUEST_MESSAGE);
    }

    /**
     * Set request message
     *
     * @param string $requestMessage
     * @return $this
     */
    public function setRequestMessage(string $requestMessage) : ValidationResultInterface
    {
        return $this->setData(self::KEY_REQUEST_MESSAGE, $requestMessage);
    }

    /**
     * Get Handle
     *
     * @return string
     */
    public function getHandle() : ?string
    {
        return $this->getData(self::KEY_HTTP_REQUEST_HANDLE);
    }

    /**
     * Set handle
     *
     * @param string|null $handle
     *
     * @return $this
     */
    public function setHandle(?string $handle) : ValidationResultInterface
    {
        return $this->setData(self::KEY_HTTP_REQUEST_HANDLE, $handle);
    }

    /**
     * Set vat id
     *
     * @param string $vatId
     * @return $this
     */
    public function setVatId(string $vatId) : ValidationResultInterface
    {
        return $this->setData(self::KEY_VAT_ID, $vatId);
    }

    /**
     * Get vat id
     *
     * @return string
     */
    public function getVatId() : ?string
    {
        return $this->getData(self::KEY_VAT_ID);
    }

    /**
     * Get is valid
     *
     * @return bool
     */
    public function getVatIsValid() : ?bool
    {
        return $this->getData(self::KEY_VAT_IS_VALID);
    }

    /**
     * Set is valid
     *
     * @param bool $isValid
     * @return $this
     */
    public function setVatIsValid(bool $isValid) : ValidationResultInterface
    {
        return $this->setData(self::KEY_VAT_IS_VALID, $isValid);
    }

    /**
     * Get request success
     *
     * @return bool
     */
    public function getVatRequestSuccess() : ?bool
    {
        return $this->getData(self::KEY_VAT_REQUEST_SUCCESS);
    }

    /**
     * Set request success
     *
     * @param bool $success
     * @return $this
     */
    public function setVatRequestSuccess(bool $success) : ValidationResultInterface
    {
        return $this->setData(self::KEY_VAT_REQUEST_SUCCESS, $success);
    }

    /**
     * Get request id
     *
     * @return string
     */
    public function getVatRequestId() : ?string
    {
        return $this->getData(self::KEY_VAT_REQUEST_ID);
    }

    /**
     * Set request id
     *
     * @param string|null $requestId
     *
     * @return $this
     */
    public function setVatRequestId(?string $requestId) : ValidationResultInterface
    {
        return $this->setData(self::KEY_VAT_REQUEST_ID, $requestId);
    }

    /**
     * Get request date
     *
     * @return string
     */
    public function getVatRequestDate() : ?string
    {
        return $this->getData(self::KEY_VAT_REQUEST_DATE);
    }

    /**
     * Set request date
     *
     * @param string $requestDate
     * @return $this
     */
    public function setVatRequestDate(string $requestDate) : ValidationResultInterface
    {
        return $this->setData(self::KEY_VAT_REQUEST_DATE, $requestDate);
    }

    /**
     * Get trader name
     *
     * @return string
     */
    public function getVatTraderName() : ?string
    {
        return $this->getData(self::KEY_VAT_TRADER_NAME);
    }

    /**
     * Set trader name
     *
     * @param string|null $vatTraderName
     *
     * @return $this
     */
    public function setVatTraderName(?string $vatTraderName) : ValidationResultInterface
    {
        return $this->setData(self::KEY_VAT_TRADER_NAME, $vatTraderName);
    }

    /**
     * Get trader address
     *
     * @return string
     */
    public function getVatTraderAddress() : ?string
    {
        return $this->getData(self::KEY_VAT_TRADER_ADDRESS);
    }

    /**
     * Set trader address
     *
     * @param string|null $vatTraderAddress
     *
     * @return $this
     */
    public function setVatTraderAddress(?string $vatTraderAddress) : ValidationResultInterface
    {
        return $this->setData(self::KEY_VAT_TRADER_ADDRESS, $vatTraderAddress);
    }

    /**
     * Get company type
     *
     * @return string
     */
    public function getVatTraderCompanyType() : ?string
    {
        return $this->getData(self::KEY_VAT_TRADER_COMPANY_TYPE);
    }

    /**
     * Set company type
     *
     * @param string|null $vatTraderCompanyType
     *
     * @return $this
     */
    public function setVatTraderCompanyType(?string $vatTraderCompanyType) : ValidationResultInterface
    {
        return $this->setData(self::KEY_VAT_TRADER_COMPANY_TYPE, $vatTraderCompanyType);
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getVatRequestCountryCode() : ?string
    {
        return $this->getData(self::KEY_VAT_REQUEST_COUNTRY_CODE);
    }

    /**
     * Set country
     *
     * @param string $vatRequestCountryCode
     *
     * @return $this
     */
    public function setVatRequestCountryCode(string $vatRequestCountryCode) : ValidationResultInterface
    {
        return $this->setData(self::KEY_VAT_REQUEST_COUNTRY_CODE, $vatRequestCountryCode);
    }

    /**
     * Get requester country
     *
     * @return string
     */
    public function getRequesterCountryCode() : ?string
    {
        return $this->getData(self::KEY_VAT_REQUESTER_COUNTRY_CODE);
    }

    /**
     * Set requester country
     *
     * @param string $vatRequestRequesterCountryCode
     * @return $this
     */
    public function setRequesterCountryCode(string $vatRequestRequesterCountryCode) : ValidationResultInterface
    {
        return $this->setData(self::KEY_VAT_REQUESTER_COUNTRY_CODE, $vatRequestRequesterCountryCode);
    }

    /**
     * Get requester number
     *
     * @return string
     */
    public function getRequesterNumber() : ?string
    {
        return $this->getData(self::KEY_VAT_REQUESTER_NUMBER);
    }

    /**
     * Set requester number
     *
     * @param string $vatRequestRequesterCountryCode
     * @return $this
     */
    public function setRequesterNumber(string $vatRequestRequesterCountryCode) : ValidationResultInterface
    {
        return $this->setData(self::KEY_VAT_REQUESTER_NUMBER, $vatRequestRequesterCountryCode);
    }
}
