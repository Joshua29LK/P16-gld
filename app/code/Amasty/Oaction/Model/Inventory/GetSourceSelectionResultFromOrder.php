<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Model\Inventory;

use Magento\Framework\Exception\LocalizedException;
use Magento\InventorySalesApi\Model\GetSkuFromOrderItemInterface;
use Magento\InventorySourceSelectionApi\Api\Data\ItemRequestInterfaceFactory;
use Magento\InventorySourceSelectionApi\Api\GetDefaultSourceSelectionAlgorithmCodeInterface;
use Magento\InventorySourceSelectionApi\Api\SourceSelectionServiceInterface;
use Magento\InventorySourceSelectionApi\Model\GetInventoryRequestFromOrder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;

class GetSourceSelectionResultFromOrder
{
    public const DEFAULT_SOURCE_CODE = 'default';
    public const REQUESTED_SKU_KEY = 'sku';
    public const REQUESTED_QTY_KEY = 'qty';

    /**
     * @var GetSkuFromOrderItemInterface
     */
    private $getSkuFromOrderItem;

    /**
     * @var ItemRequestInterfaceFactory
     */
    private $itemRequestFactory;

    /**
     * @var GetDefaultSourceSelectionAlgorithmCodeInterface
     */
    private $getDefaultSourceSelectionAlgorithmCode;

    /**
     * @var SourceSelectionServiceInterface
     */
    private $sourceSelectionService;

    /**
     * @var GetInventoryRequestFromOrder
     */
    private $getInventoryRequestFromOrder;

    /**
     * @var array
     */
    private $selectionRequestItemsData = [];

    public function __construct(
        GetSkuFromOrderItemInterface $getSkuFromOrderItem,
        ItemRequestInterfaceFactory $itemRequestFactory,
        GetDefaultSourceSelectionAlgorithmCodeInterface $getDefaultSourceSelectionAlgorithmCode,
        SourceSelectionServiceInterface $sourceSelectionService,
        GetInventoryRequestFromOrder $getInventoryRequestFromOrder
    ) {
        $this->getSkuFromOrderItem = $getSkuFromOrderItem;
        $this->itemRequestFactory = $itemRequestFactory;
        $this->getDefaultSourceSelectionAlgorithmCode = $getDefaultSourceSelectionAlgorithmCode;
        $this->sourceSelectionService = $sourceSelectionService;
        $this->getInventoryRequestFromOrder = $getInventoryRequestFromOrder;
    }

    /**
     * @throws LocalizedException
     */
    public function execute(OrderInterface $order): ?string
    {
        $sourceCode = null;
        $this->prepareSelectionRequestItemsData($order->getItems());

        $requestItemsCount = count($this->selectionRequestItemsData);
        if ($requestItemsCount) {
            $sources = [];
            $inventoryRequest = $this->getInventoryRequestFromOrder->execute(
                (int) $order->getEntityId(),
                $this->getSelectionRequestItems()
            );

            $selectionAlgorithmCode = $this->getDefaultSourceSelectionAlgorithmCode->execute();
            $sourceSelectionResult = $this->sourceSelectionService->execute(
                $inventoryRequest,
                $selectionAlgorithmCode
            );

            foreach ($sourceSelectionResult->getSourceSelectionItems() as $item) {
                $requestedQty = $this->selectionRequestItemsData[$item->getSku()][self::REQUESTED_QTY_KEY];
                if ($requestedQty <= $item->getQtyAvailable()) {
                    if (array_key_exists($item->getSourceCode(), $sources)) {
                        ++$sources[$item->getSourceCode()];
                    } else {
                        $sources[$item->getSourceCode()] = 1;
                    }
                }
            }

            if ($sources) {
                foreach ($sources as $code => $itemsQty) {
                    if ($requestItemsCount === $itemsQty) {
                        $sourceCode = $code;
                        break;
                    }
                }

                if (!$sourceCode) {
                    throw new LocalizedException(
                        __('A shipment can\'t be created when order items have different sources.')
                    );
                }
            } else {
                $sourceCode = self::DEFAULT_SOURCE_CODE;
            }
        }

        return $sourceCode;
    }

    private function prepareSelectionRequestItemsData(array $orderItems): void
    {
        foreach ($orderItems as $orderItem) {
            if ($orderItem->isDummy() || $orderItem->getIsVirtual()) {
                continue;
            }

            $itemSku = $this->getSkuFromOrderItem->execute($orderItem);
            $qty = $this->castQty($orderItem, $orderItem->getQtyToShip());

            if (!isset($this->selectionRequestItemsData[$itemSku])) {
                $this->selectionRequestItemsData[$itemSku] = [
                    self::REQUESTED_SKU_KEY => $itemSku,
                    self::REQUESTED_QTY_KEY => $qty,
                ];
            } else {
                $this->selectionRequestItemsData[$itemSku][self::REQUESTED_QTY_KEY] += $qty;
            }
        }
    }

    private function getSelectionRequestItems(): array
    {
        $selectionRequestItems = [];

        foreach ($this->selectionRequestItemsData as $itemData) {
            $selectionRequestItems[] = $this->itemRequestFactory->create($itemData);
        }

        return $selectionRequestItems;
    }

    /**
     * Cast qty value
     *
     * @param OrderItemInterface $item
     * @param string|int|float $qty
     * @return float
     */
    private function castQty(OrderItemInterface $item, $qty): float
    {
        if ($item->getIsQtyDecimal()) {
            $qty = (float) $qty;
        } else {
            $qty = (int) $qty;
        }

        return max($qty, 0);
    }
}
