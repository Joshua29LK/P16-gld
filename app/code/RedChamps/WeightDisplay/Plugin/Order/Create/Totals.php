<?php
namespace RedChamps\WeightDisplay\Plugin\Order\Create;

use Magento\Sales\Block\Adminhtml\Order\Create\Totals as TotalsCore;

class Totals
{
    /**
     * @param TotalsCore $subject
     * @param $result
     */
    public function afterRenderTotal(TotalsCore $subject, $result, $total)
    {
        $block = $subject->getLayout()->getBlock('order.create.total.weight');
        $weight = ($block && $total->getCode() == "subtotal") ? $block->toHtml() : "";
        //$subject->getLayout()->unsetElement('order.create.total.weight');
        return $weight . $result;
    }
}
