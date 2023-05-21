<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Archive for Magento 2
*/

namespace Amasty\Orderarchive\Observer;

use Amasty\Orderarchive\Helper\Data;
use Magento\Framework\Event\ObserverInterface;

class OrderLoadAfter implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var \Amasty\Orderarchive\Model\ResourceModel\OrderGrid
     */
    protected $orderGrid;

    /**
     * GetCollection constructor.
     * @param Data $helper
     */
    public function __construct(
        Data $helper,
        \Amasty\Orderarchive\Model\ResourceModel\OrderGrid $orderGrid
    ) {
        $this->helper = $helper;
        $this->orderGrid = $orderGrid;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return mixed
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper
            ->getConfigValueByPath(\Amasty\Orderarchive\Helper\Data::CONFIG_PATH_GENERAL_ENABLE_MASSFILTER)) {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $observer->getOrder();

            if ($this->orderGrid->isArchived($order->getId())) {
                $order->reset();
            }
        }
    }
}
