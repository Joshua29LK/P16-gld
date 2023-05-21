<?php
namespace Bss\FomartOrderDeliveryDate\Override\Model;

use Magento\Directory\Model\Currency;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\ResourceModel\Order\Address\Collection;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection as CreditmemoCollection;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection as InvoiceCollection;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as ImportCollection;
use Magento\Sales\Model\ResourceModel\Order\Payment\Collection as PaymentCollection;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Collection as ShipmentCollection;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Track\Collection as TrackCollection;
use Magento\Sales\Model\ResourceModel\Order\Status\History\Collection as HistoryCollection;

class Order extends \Magento\Sales\Model\Order
{
    /**
     * @var ResolverInterface
     */
    private $localeResolver;

    public function getShippingArrivalDate()
    {
        $helperBss = ObjectManager::getInstance()->get('Bss\OrderDeliveryDate\Helper\Data');
        $date = $this->getData('shipping_arrival_date');
        if ($date != null && $date != '') {
            return $helperBss->formatDate($this->getData('shipping_arrival_date'));
        }
        return '';
    }
}
