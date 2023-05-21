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

namespace Geissweb\Euvat\Plugin\Tax;

use Geissweb\Euvat\Helper\Configuration;
use Geissweb\Euvat\Helper\Functions;
use Geissweb\Euvat\Helper\Threshold\Calculator;
use Geissweb\Euvat\Logger\Logger;
use Geissweb\Euvat\Model\Validation;
use Geissweb\Euvat\Model\ValidationRepository;
use Geissweb\Euvat\Helper\VatNumber\Formatter;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Calculation does all the tricky VAT stuff
 */
class Calculation
{
    /**
     * @var Validation
     */
    public $validationModel;

    /**
     * @var ValidationRepository
     */
    public $validationRepository;

    /**
     * @var Configuration
     */
    public $configHelper;

    /**
     * @var Functions
     */
    public $functionsHelper;

    /**
     * @var Logger
     */
    public $logger;

    /**
     * @var ExtensibleDataObjectConverter
     */
    public $converter;

    /**
     * @var Session
     */
    public $customerSession;

    /**
     * @var ManagerInterface
     */
    public $eventManager;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var Formatter
     */
    private $vatNumberFormatter;
    /**
     * @var Calculator
     */
    private $thresholdCalculator;

    /**
     * TaxCalculation constructor.
     *
     * @param Validation                    $validationModel
     * @param ValidationRepository          $validationRepository
     * @param Formatter                     $vatNumberFormatter
     * @param Configuration                 $configHelper
     * @param Functions                     $functionsHelper
     * @param Logger                        $logger
     * @param ManagerInterface              $eventManager
     * @param ExtensibleDataObjectConverter $converter
     * @param Session                       $customerSession
     * @param CustomerRepositoryInterface   $customerRepository
     * @param Calculator                    $thresholdCalculator
     */
    public function __construct(
        Validation $validationModel,
        ValidationRepository $validationRepository,
        Formatter $vatNumberFormatter,
        Configuration $configHelper,
        Functions $functionsHelper,
        Logger $logger,
        ManagerInterface $eventManager,
        ExtensibleDataObjectConverter $converter,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        Calculator $thresholdCalculator
    ) {
        $this->validationModel = $validationModel;
        $this->validationRepository = $validationRepository;
        $this->configHelper = $configHelper;
        $this->functionsHelper = $functionsHelper;
        $this->logger = $logger;
        $this->eventManager = $eventManager;
        $this->converter = $converter;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->vatNumberFormatter = $vatNumberFormatter;
        $this->thresholdCalculator = $thresholdCalculator;
    }

    /**
     * Modify the tax rate request as needed
     *
     * @param \Magento\Tax\Model\Calculation $subject
     * @param callable                       $proceed
     * @param null                           $shippingAddress
     * @param null                           $billingAddress
     * @param null                           $customerTaxClass
     * @param null                           $store
     * @param null                           $customerId
     *
     * @return mixed
     */
    public function aroundGetRateRequest(
        \Magento\Tax\Model\Calculation $subject,
        callable $proceed,
        $shippingAddress = null,
        $billingAddress = null,
        $customerTaxClass = null,
        $store = null,
        $customerId = null
    ) {
        if (!$this->configHelper->getUseVatCalculation()) {
            $this->logger->customLog("aroundGetRateRequest dynamic tax class application is disabled.");
            return $proceed($shippingAddress, $billingAddress, $customerTaxClass, $store, $customerId);
        }

        /**@var $billingAddress \Magento\Customer\Model\Data\Address */
        /**@var $shippingAddress \Magento\Customer\Model\Data\Address */
        $basedOn = $this->configHelper->getVatBasedOn();
        $this->logger->customLog("aroundGetRateRequest based on: $basedOn address for customer id: $customerId");
        if ($basedOn == 'shipping') {
            if (($shippingAddress === false || $shippingAddress === null)
                || (is_object($shippingAddress) && $shippingAddress->getCountryId() === null)
                //Need to use the default address in this case
            ) {
                if ($customerId !== null) {
                    $this->logger->customLog("aroundGetRateRequest using default $basedOn address");
                    $shippingAddress = $this->customerSession->getCustomer()->getDefaultShippingAddress();
                } else {
                    $this->logger->customLog("aroundGetRateRequest return $basedOn proceed");
                    return $proceed($shippingAddress, $billingAddress, $customerTaxClass, $store, $customerId);
                }
            }
            $basedOnAddress = $shippingAddress;
        } elseif ($basedOn == 'billing') {
            if (($billingAddress === false || $billingAddress === null)
                || (is_object($billingAddress) && $billingAddress->getCountryId() === null)
                //Need to use the default address in this case
            ) {
                if ($customerId !== null) {
                    $this->logger->customLog("aroundGetRateRequest using default $basedOn address");
                    $billingAddress = $this->customerSession->getCustomer()->getDefaultBillingAddress();
                } else {
                    $this->logger->customLog("aroundGetRateRequest return $basedOn proceed");
                    return $proceed($shippingAddress, $billingAddress, $customerTaxClass, $store, $customerId);
                }
            }
            $basedOnAddress = $billingAddress;
        } else {
            $this->logger->customLog("aroundGetRateRequest return $basedOn proceed");
            return $proceed($shippingAddress, $billingAddress, $customerTaxClass, $store, $customerId);
        }

        if (!$basedOnAddress) {
            return $proceed($shippingAddress, $billingAddress, $customerTaxClass, $store, $customerId);
        }

        $this->logger->debugAddress($basedOnAddress);
        $vatId = $basedOnAddress->getVatId();
        $shopCc = $this->configHelper->getMerchantCountryCode();
        $customerCountryCode = $basedOnAddress->getCountryId();
        $this->logger->customLog("aroundGetRateRequest vatId: $vatId");
        $this->logger->customLog("aroundGetRateRequest shopCc $shopCc");
        $this->logger->customLog("aroundGetRateRequest customerCountryCode $customerCountryCode");

        try {
            $currentCustomerGroup = $this->customerSession->getCustomerGroupId();
            if ($this->customerSession->isLoggedIn()) {
                $currentCustomerGroup = $this->customerSession->getCustomer()->getGroupId();
            } elseif (!empty($customerId)) { //for the admin create order
                $customer = $this->customerRepository->getById($customerId);
                $currentCustomerGroup = $customer->getGroupId();
            }
            $this->logger->customLog("aroundGetRateRequest Current group id: $currentCustomerGroup");
            if ($this->configHelper->isNoDynamicGroup((int)$currentCustomerGroup)) {
                $this->logger->customLog("aroundGetRateRequest return proceed because of no dynamic tax group");
                $noDynamicRequest = $proceed($shippingAddress, $billingAddress, $customerTaxClass, $store, $customerId);
                return $this->applyThresholdCountry($noDynamicRequest, $customerCountryCode);
            }
        } catch (NoSuchEntityException|LocalizedException $e) {
            $this->logger->customLog("aroundGetRateRequest Current group does not exist: " . $e->getMessage());
            $this->logger->critical($e);
        }

        $this->eventManager->dispatch(
            'estimate_tax_based_on_country',
            ['country_code' => $customerCountryCode]
        );

        $taxClassToSet = $this->configHelper->getConsumerTaxClass();

        if (!empty($vatId)) {
            $customerVatNumberCc = $this->vatNumberFormatter->extractCountryIdFromVatId($vatId);

            /** @var Validation $validation */
            $validation = $this->validationRepository->getByVatId($vatId);

            if ($validation && $validation->getVatRequestSuccess() == true) {
                $this->logger->customLog("aroundGetRateRequest Validation successful.");
                if ($validation->getVatIsValid() == true
                    && ($shopCc != $customerCountryCode)
                    && ($this->configHelper->isEuCountry($customerCountryCode) || $customerCountryCode === 'GB')
                ) {
                    $isAlwaysVatCountry = $this->configHelper->isAlwaysVatCountry($customerCountryCode);
                    //Customer has valid VAT-ID and is not domestic
                    $this->logger->customLog("aroundGetRateRequest Customer has valid VAT-ID and is not domestic.");
                    if ($customerVatNumberCc == $customerCountryCode
                        && !$isAlwaysVatCountry
                    ) {
                        $taxClassToSet = $this->configHelper->getExcludingTaxClass();
                    } else {
                        $this->logger->customLog("aroundGetRateRequest VAT-ID not matching address country
                                            / always calculate VAT country (" . (int)$isAlwaysVatCountry . ")");
                        $taxClassToSet = $this->configHelper->getConsumerTaxClass();
                    }
                } elseif ($validation->getVatIsValid() == true && $shopCc == $customerCountryCode) {
                    //Customer has valid VAT-ID and is domestic
                    $this->logger->customLog("aroundGetRateRequest Customer has valid VAT-ID and is domestic.");
                    $taxClassToSet = $this->configHelper->getBusinessTaxClass();
                } elseif ($validation->getVatIsValid() == false) {
                    $this->logger->customLog("aroundGetRateRequest Invalid VAT-ID.");
                    $taxClassToSet = $this->configHelper->getConsumerTaxClass();
                }
            } elseif ($validation && $validation->getVatRequestSuccess() == false) {
                $this->logger->customLog("aroundGetRateRequest Validation request invalid.");
                $taxClassToSet = $this->configHelper->getConsumerTaxClass();

                if ($validation->getVatRequestId() === 'OFFLINE'
                   && $this->configHelper->isOfflineValidationEnabled()
                   && $this->configHelper->isOfflineValidationCountry($customerCountryCode)
                ) {
                    $this->logger->customLog(
                        "aroundGetRateRequest Offline validation enabled for $customerCountryCode"
                    );
                    if ($shopCc == $customerCountryCode) {
                        $taxClassToSet = $this->configHelper->getBusinessTaxClass();
                    } else {
                        $taxClassToSet = $this->configHelper->getExcludingTaxClass();
                    }
                }
            }
        }

        if ($this->configHelper->isUkThresholdEnabled()
            && $shopCc !== 'GB'
            && $taxClassToSet == $this->configHelper->getConsumerTaxClass()
        ) {
            $this->thresholdCalculator->setConfigSection('brexit_settings');

            if ($this->thresholdCalculator->isDeliveryToUk()
                && $this->thresholdCalculator->isCurrentCartAbove(
                    $this->configHelper->getUkThresholdValue(),
                    'GBP'
                )
            ) {
                $this->logger->customLog("aroundGetRateRequest UK Threshold exceeded");
                $taxClassToSet = $this->configHelper->getExcludingTaxClass();
            }
        }

        if ($this->configHelper->isEUThresholdEnabled()
            && (!$this->configHelper->isEuCountry($shopCc) || $shopCc === 'GB') //GB should be in EU country list for NI
            && $taxClassToSet == $this->configHelper->getConsumerTaxClass()
        ) {
            $this->thresholdCalculator->setConfigSection('ioss_settings');
            if ($this->thresholdCalculator->isDeliveryToEU()
                && !$this->thresholdCalculator->isDeliveryToNI()
                && $this->thresholdCalculator->isCurrentCartAbove(
                    $this->configHelper->getEUThresholdValue(),
                    'EUR'
                )
            ) {
                $this->logger->customLog("aroundGetRateRequest IOSS Threshold exceeded");
                $taxClassToSet = $this->configHelper->getExcludingTaxClass();
            }
        }

        $this->logger->customLog("aroundGetRateRequest evaluated class id: " . $taxClassToSet);
        $returnRequest = $proceed($shippingAddress, $billingAddress, $taxClassToSet, $store, $customerId);
        return $this->applyThresholdCountry($returnRequest, $customerCountryCode);
    }

    /**
     * Changes the request country to the threshold country
     *
     * @param $request
     * @param string $customerCountryCode
     *
     * @return mixed
     */
    private function applyThresholdCountry($request, string $customerCountryCode)
    {
        // Calculate tax amount based on threshold country if needed
        $shopCc = $this->configHelper->getMerchantCountryCode();
        if ($request->getCountryId() == $shopCc &&
            $this->configHelper->getEnableThresholdCountries()
            && $this->configHelper->isThresholdCountry($customerCountryCode)
            && $this->configHelper->isCbtEnabled()
        ) {
            $this->logger->customLog("aroundGetRateRequest CBT enabled and threshold country $customerCountryCode.");
            $request->setCountryId($customerCountryCode);
        }

        $this->logger->customLog("aroundGetRateRequest final request is", $request->getData());

        return $request;
    }
}
