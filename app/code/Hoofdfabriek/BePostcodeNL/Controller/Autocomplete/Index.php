<?php
namespace Hoofdfabriek\BePostcodeNL\Controller\Autocomplete;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Index
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var \Hoofdfabriek\PostcodeNL\Model\PostcodeNL
     */
    private $postcodeApi;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * Index constructor.
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Hoofdfabriek\PostcodeNL\Model\PostcodeNL $postcodeNL
     * @param ScopeConfigInterface $config
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Action\Context $context,
        \Hoofdfabriek\PostcodeNL\Model\PostcodeNL $postcodeNL,
        ScopeConfigInterface $config
    ) {
        parent::__construct($context);

        $this->config = $config;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->postcodeApi = $postcodeNL;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        switch ($this->_request->getParam('type')) {
            case 'postal-area':
                $result->setData($this->postcodeApi->belgiumPostAreaAutocomplete(
                    $this->_request->getParam('t')
                ));
                break;
            case 'street':
                $result->setData($this->postcodeApi->belgiumStreetAutocomplete(
                    $this->_request->getParam('municipalityNisCode'),
                    $this->_request->getParam('street'),
                    $this->_request->getParam('postcode')
                ));
                break;
            case 'house':
                $result->setData($this->postcodeApi->belgiumHouseNumberAutocomplete(
                    $this->_request->getParam('streetId'),
                    $this->_request->getParam('postcode'),
                    $this->_request->getParam('houseNumber'),
                    $this->config->getValue('postcodenl/autocomplete/belgium_validation', ScopeInterface::SCOPE_STORES)
                ));
        }

        return $result;
    }
}
