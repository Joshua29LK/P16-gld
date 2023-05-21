<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Archive for Magento 2
*/
declare(strict_types=1);

namespace Amasty\Orderarchive\Plugin;

use Amasty\Orderarchive\Api\ArchiveProcessorInterface;
use Amasty\Orderarchive\Model\ResourceModel\OrderGrid;
use Magento\Sales\Model\ResourceModel\Grid;

class GridRefreshPlugin
{
    /**
     * @var OrderGrid
     */
    private $orderArchive;

    /**
     * @var ArchiveProcessorInterface
     */
    private $orderProcessor;

    /**
     * @var int
     */
    protected $orderId;

    public function __construct(
        OrderGrid $orderArchive,
        ArchiveProcessorInterface $orderProcessor
    ) {
        $this->orderArchive = $orderArchive;
        $this->orderProcessor = $orderProcessor;
    }

    /**
     * If order is archived - disable original grid updates
     */
    public function aroundRefresh(
        Grid $subject,
        \Closure $proceed,
        $value,
        $field = null
    ) {
        $isOrderGrid = (bool)stripos($subject->getGridTable(), 'order');

        if ($isOrderGrid) {
            $this->orderId = (int)$value;
        }

        if ($this->orderId && $this->orderArchive->isArchived($this->orderId)) {
            //for order update (invoice, memo, etc) from archive reload order to archive
            $this->orderProcessor->removeFromArchive([$this->orderId]);
            $this->orderProcessor->moveToArchive([$this->orderId]);
        } else {
            $proceed($value, $field);
        }
    }
}
