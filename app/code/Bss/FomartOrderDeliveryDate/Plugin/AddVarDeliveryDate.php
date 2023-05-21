<?php
namespace Bss\FomartOrderDeliveryDate\Plugin;

class AddVarDeliveryDate
{
    public function __construct(
        \Bss\OrderDeliveryDate\Helper\Data $helperBss
    ) {
        $this->helperBss = $helperBss;
    }

    /**
     * @return void
     */
    public function beforeSetTemplateVars($subject, array $vars) {
        
        //get order
        if (isset($vars['order']) && $vars['order'] instanceof \Magento\Sales\Model\Order) {
            $order = $vars['order'];
            $shipping_arrival_date = $order->getData('shipping_arrival_date');
            if ($shipping_arrival_date != null && $shipping_arrival_date != '') {
                $vars['shipping_arrival_date'] = $this->helperBss->formatDate($shipping_arrival_date);
            }
        }
       
        return [$vars];
    }
}
