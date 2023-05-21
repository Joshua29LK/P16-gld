<?php
/**
 * Created by RedChamps.
 * User: Rav
 * Date: 06/04/18
 * Time: 8:24 PM
 */
namespace RedChamps\TotalAdjustment\Plugin;

class InvoiceGet
{
    public function afterGet(
        \Magento\Sales\Api\InvoiceRepositoryInterface $subject,
        \Magento\Sales\Api\Data\InvoiceInterface $resultInvoice
    ) {
        $resultOrder = $this->getFoomanAttribute($resultInvoice);

        return $resultOrder;
    }

    private function getFoomanAttribute(\Magento\Sales\Api\Data\InvoiceInterface $invoice)
    {
        var_dump(get_class($invoice));
        exit;

//        $extensionAttributes = $order->getExtensionAttributes();
//        $orderExtension = $extensionAttributes ? $extensionAttributes : $this->orderExtensionFactory->create();
//        $foomanAttribute = $this->foomanAttributeFactory->create();
//        $foomanAttribute->setValue($foomanAttributeValue);
//        $orderExtension->setFoomanAttribute($foomanAttribute);
//        $order->setExtensionAttributes($orderExtension);
//
//        return $order;
    }
}