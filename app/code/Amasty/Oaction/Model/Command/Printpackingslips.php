<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Model\Command;

use Amasty\Oaction\Helper\Data;
use Amasty\Oaction\Model\Command;
use Amasty\Oaction\Model\Command\Pdf\PdfCollector\PdfCollectorInterface;
use Amasty\Oaction\Model\Command\Pdf\PdfCollectorResolver;
use Amasty\Oaction\Model\Order\Pdf\OrderPackingSlip as PdfOrderPackingSlip;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Pdf\Shipment as PdfShipment;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Collection as ShipmentCollection;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;

class Printpackingslips extends Command
{
    /**
     * @var Data
     */
    private $oActionHelper;

    /**
     * @var PdfShipment
     */
    private $pdfShipment;

    /**
     * @var PdfOrderPackingSlip
     */
    private $pdfOrderPackingSlip;

    /**
     * @var ShipmentCollectionFactory
     */
    private $shipmentCollectionFactory;

    /**
     * @var PdfCollectorInterface
     */
    private $pdfCollector;

    public function __construct(
        Data $oActionHelper,
        PdfShipment $pdfShipment,
        PdfOrderPackingSlip $pdfOrderPackingSlip,
        ShipmentCollectionFactory $shipmentCollectionFactory,
        PdfCollectorResolver $pdfCollectorResolver
    ) {
        parent::__construct();
        $this->oActionHelper = $oActionHelper;
        $this->pdfShipment = $pdfShipment;
        $this->pdfOrderPackingSlip = $pdfOrderPackingSlip;
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->pdfCollector = $pdfCollectorResolver->get();
    }

    /**
     * @param AbstractCollection $collection
     * @param int $notifyCustomer
     * @param array $oaction
     * @return string
     */
    public function execute(AbstractCollection $collection, int $notifyCustomer = 0, array $oaction = []): string
    {
        $pdfs = [];
        $numItems = 0;
        foreach ($this->getPreparedCollections($collection) as $toPdfCollection) {
            // phpcs:ignore Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
            $pdfs = array_merge($pdfs, $this->getPdfs($toPdfCollection, $this->pdfCollector->useSeparateFiles()));
            $numItems += $toPdfCollection->count();
        }

        if (!$numItems) {
            throw new LocalizedException(__('There are no printable documents related to selected orders.'));
        }

        $this->pdfCollector = $this->pdfCollector->collect(array_filter($pdfs));

        return __(
            'Total of %1 item(s) has been successfully processed.',
            $numItems
        )->render();
    }

    public function hasResponse(): bool
    {
        return $this->pdfCollector->hasResponse();
    }

    public function getResponseName(): string
    {
        return 'packingslip_' . $this->oActionHelper->getDate() . '.'
            . $this->pdfCollector->getExtension();
    }

    public function getResponseBody(): string
    {
        return $this->pdfCollector->render();
    }

    private function getPreparedCollections(AbstractCollection $orderCollection): array
    {
        $shipmentCollection = $this->shipmentCollectionFactory
            ->create()
            ->setOrderFilter(['in' => $orderCollection->getAllIds()]);

        $orderCollection->clear()
            ->getSelect()
            ->joinLeft(
                ['shipment' => $orderCollection->getTable('sales_shipment')],
                'main_table.entity_id = shipment.order_id',
                null
            )->where('shipment.entity_id IS NULL');

        foreach ($orderCollection->getItems() as $id => $order) {
            if (!$this->checkOrder($order)) {
                $orderCollection->removeItemByKey($id);
            }
        }

        return [$shipmentCollection, $orderCollection];
    }

    private function checkOrder(Order $order): bool
    {
        if (!$order->getId()) {
            $this->_errors[] = __('The order no longer exists.');

            return false;
        }

        if (!$order->canShip()) {
            $this->_errors[] = __(
                'The packing slip can\'t be printed for the order "%1".',
                $order->getIncrementId()
            );

            return false;
        }

        return true;
    }

    /**
     * @param OrderCollection|ShipmentCollection|AbstractCollection $collection
     * @param bool $useSeparateFiles
     * @return \Zend_Pdf[]
     */
    private function getPdfs(AbstractCollection $collection, bool $useSeparateFiles = false): array
    {
        $pdfs = [];
        $pdfObject = $collection instanceof ShipmentCollection
            ? $this->pdfShipment
            : $this->pdfOrderPackingSlip;

        if ($useSeparateFiles) {
            foreach ($collection as $item) {
                $pdfs[$item->getEntityType() . '_' . $item->getIncrementId()] = $pdfObject->getPdf([$item]);
            }
        } else {
            $pdfs[] = $pdfObject->getPdf($collection->getItems());
        }

        return $pdfs;
    }
}
