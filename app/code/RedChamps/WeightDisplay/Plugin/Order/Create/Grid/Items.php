<?php
namespace RedChamps\WeightDisplay\Plugin\Order\Create\Grid;

use Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid;

class Items
{
    public function afterGetConfigureButtonHtml(Grid $subject, $result, $item)
    {
        $block = $subject->getLayout()->getBlock('order.create.item.weight');
        $weight = $block ? $block->setItem($item)->toHtml() : "";
        return $weight . $result;
    }
}
