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

namespace Geissweb\Euvat\Observer;

use Geissweb\Euvat\Api\Data\ValidationInterface;
use Geissweb\Euvat\Api\Data\ValidationInterfaceFactory;
use Geissweb\Euvat\Api\Data\ValidationResultInterface;
use Geissweb\Euvat\Logger\Logger;
use Geissweb\Euvat\Model\ValidationRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class VatValidationAfter
 * Creates or updates VAT number validation data
 */
class VatValidationAfter implements ObserverInterface
{
    /**
     * @var \Geissweb\Euvat\Api\Data\ValidationInterfaceFactory
     */
    public $validationInterfaceFactory;

    /**
     * @var ValidationRepository
     */
    public $validationRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    public $search;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Constructor
     *
     * @param ValidationInterfaceFactory $validationInterfaceFactory
     * @param ValidationRepository $validationRepository
     * @param Logger $logger
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ValidationInterfaceFactory $validationInterfaceFactory,
        ValidationRepository $validationRepository,
        Logger $logger,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->validationInterfaceFactory = $validationInterfaceFactory;
        $this->validationRepository = $validationRepository;
        $this->search = $searchCriteriaBuilder;
        $this->logger = $logger;
    }

    /**
     * Do the job
     *
     * @param Observer $observer
     *
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $this->logger->customLog("VatValidationAfter Start");

        /** @var \Geissweb\Euvat\Model\Validation\Result $result */
        $result = $observer->getEvent()->getValidationResult();

        $searchCriteria = $this->search->addFilter(
            'vat_id',
            $result->getVatRequestCountryCode() . $result->getVatId(),
            'eq'
        )->create();
        $validationList = $this->validationRepository->getList($searchCriteria);
        /** @var ValidationInterface[] $items */
        $items = $validationList->getItems();

        $this->logger->customLog("VatValidationAfter checking VAT number: "
                             . $result->getVatRequestCountryCode() . $result->getVatId());

        if ($validationList->getTotalCount() > 0) {
            $this->logger->customLog("VatValidationAfter updating validation information.");
            foreach ($items as $item) {
                $this->setValidationData($item, $result);
                try {
                    $this->validationRepository->save($item);
                } catch (LocalizedException $exc) {
                    $this->logger->critical($exc);
                }
            }
        } else {
            $this->logger->customLog("VatValidationAfter creating validation information.");
            $validation = $this->validationInterfaceFactory->create();
            $this->setValidationData($validation, $result);
            try {
                $this->validationRepository->save($validation);
            } catch (LocalizedException $exc) {
                $this->logger->critical($exc);
            }
        }

        $this->logger->customLog("VatValidationAfter End");
    }

    /**
     * Sets ValidationResult data to Validation
     *
     * @param \Geissweb\Euvat\Api\Data\ValidationInterface $item
     * @param \Geissweb\Euvat\Api\Data\ValidationResultInterface $result
     *
     * @return void
     */
    private function setValidationData(ValidationInterface $item, ValidationResultInterface $result): void
    {
        $item->setHandle($result->getHandle());
        $item->setVatId($result->getVatRequestCountryCode().$result->getVatId());
        $item->setVatIsValid($result->getVatIsValid());
        $item->setVatRequestDate($result->getVatRequestDate());
        $item->setVatRequestId($result->getVatRequestId());
        $item->setVatRequestSuccess($result->getVatRequestSuccess());
        $item->setVatTraderName($result->getVatTraderName());
        $item->setVatTraderAddress($result->getVatTraderAddress());
        $item->setRequestMessage($result->getRequestMessage());
    }
}
