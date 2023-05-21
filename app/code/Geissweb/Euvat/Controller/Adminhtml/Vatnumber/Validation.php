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

namespace Geissweb\Euvat\Controller\Adminhtml\Vatnumber;

use Geissweb\Euvat\Helper\Configuration;
use Geissweb\Euvat\Helper\Functions;
use Geissweb\Euvat\Logger\Logger;
use Geissweb\Euvat\Validator\Service;
use Geissweb\Euvat\Helper\VatNumber\Formatter;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\Data\AddressInterfaceFactory as AddressFactory;
use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Intl\DateTimeFactory;

/**
 * Class Validation executes validation for the admin area
 */
class Validation extends Action
{
    /**
     * @var Configuration
     */
    public $configHelper;

    /**
     * @var Context
     */
    public $context;

    /**
     * @var Logger
     */
    public $logger;

    /**
     * @var Functions
     */
    public $functionsHelper;

    /**
     * @var DataObjectFactory
     */
    public $dataObjectFactory;

    /**
     * @var DateTimeFactory
     */
    public $dateTimeFactory;

    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @var Service
     */
    private $service;

    /**
     * @var AddressFactory
     */
    private $addressFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param AddressFactory $addressFactory
     * @param Configuration $configHelper
     * @param Functions $functionsHelper
     * @param Formatter $vatNumberFormatter
     * @param Service $service
     * @param Logger $logger
     *
     * @internal param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        Context $context,
        AddressFactory $addressFactory,
        Configuration $configHelper,
        Functions $functionsHelper,
        Formatter $vatNumberFormatter,
        Service $service,
        Logger $logger
    ) {
        parent::__construct($context);
        $this->context = $context;
        $this->configHelper = $configHelper;
        $this->functionsHelper = $functionsHelper;
        $this->logger = $logger;
        $this->formatter = $vatNumberFormatter;
        $this->service = $service;
        $this->addressFactory = $addressFactory;
    }

    /**
     * Execute VAT number validation
     *
     * @return JsonResult|Raw
     * @throws \SoapFault
     */
    public function execute()
    {
        if ($this->getRequest()->getMethod() !== 'POST'
            || !$this->getRequest()->isXmlHttpRequest()
            || !$this->_formKeyValidator->validate($this->getRequest())
        ) {
            /** @var Raw $rawResult */
            $rawResult = $this->resultFactory->create(ResultFactory::TYPE_RAW)->setHttpResponseCode(400);
            return $rawResult;
        }

        $vatNumber = $this->getRequest()->getPost('vat_number');
        $vatNumberFromSales = $this->getRequest()->getPost('vat');
        if (empty($vatNumber) && !empty($vatNumberFromSales)) {
            $vatNumber = $vatNumberFromSales;
        }

        $address = $this->addressFactory->create();
        $address->setCountryId($this->formatter->extractCountryIdFromVatId($vatNumber));
        $vatCc = substr($vatNumber, 0, 2);
        $vatNumberWithoutCc = str_replace($vatCc, '', $vatNumber);

        $result = $this->service->validate($vatCc, $vatNumberWithoutCc);
        $response = [
            'group' => $this->functionsHelper->getCustomerGroup($address, $result),
            'valid' => (bool)$result->getVatIsValid(),
            'vat_is_valid' => (bool)$result->getVatIsValid(),
            'success' => (bool)$result->getVatRequestSuccess(),
            'trader_name' => $result->getVatTraderName(),
            'trader_address' => $result->getVatTraderAddress(),
            'vat_request_date' => $result->getVatRequestDate(),
            'vat_request_id' => $result->getVatRequestId(),
            'request_message' => $result->getRequestMessage()
        ];

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($response);
    }

    /**
     * @inheritdoc
     */
    public function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('Geissweb_Euvat::validation_usage');
    }
}
