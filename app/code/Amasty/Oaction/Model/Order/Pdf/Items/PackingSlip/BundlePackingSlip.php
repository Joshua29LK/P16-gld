<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Model\Order\Pdf\Items\PackingSlip;

use Magento\Bundle\Model\Sales\Order\Pdf\Items\Shipment;

class BundlePackingSlip extends Shipment
{
    /**
     * Getting all available children for Order item and correct Qty value
     *
     * @param \Magento\Framework\DataObject $item
     * @return array
     */
    public function getChildren($item)
    {
        $itemsArray = [];
        $items = $item->getOrder()->getAllItems();

        if ($items) {
            foreach ($items as $value) {
                $value->setQty($value->getQtyOrdered());
                $parentItem = $value->getParentItem();
                if ($parentItem) {
                    $itemsArray[$parentItem->getId()][$value->getId()] = $value;
                } else {
                    $itemsArray[$value->getId()][$value->getId()] = $value;
                }
            }
        }

        if (isset($itemsArray[$item->getId()])) {
            return $itemsArray[$item->getId()];
        }

        return null;
    }
}
