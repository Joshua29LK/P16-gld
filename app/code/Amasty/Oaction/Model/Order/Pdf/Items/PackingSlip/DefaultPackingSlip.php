<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Model\Order\Pdf\Items\PackingSlip;

use Magento\Sales\Model\Order\Pdf\Items\Shipment\DefaultShipment;

class DefaultPackingSlip extends DefaultShipment
{
    /**
     * Draw item line and correct Qty value
     *
     * @return void
     */
    public function draw(): void
    {
        $item = $this->getItem();
        $item->setQty($item->getQtyOrdered());
        $this->setItem($item);

        parent::draw();
    }
}
