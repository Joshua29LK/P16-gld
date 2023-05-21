<?php

namespace MageArray\Customprice\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Custompriceproductloadafter implements ObserverInterface
{
    /**
     * @var \MageArray\Customprice\Model\CsvpriceFactory
     */
    protected $_csvpriceFactory;

    /**
     * [__construct description]
     *
     * @param \MageArray\Customprice\Model\ResourceModel\Csvprice $csvpriceFactory [description]
     */
    public function __construct(
        \MageArray\Customprice\Model\ResourceModel\Csvprice $csvpriceFactory
    ) {
        $this->_csvpriceFactory = $csvpriceFactory;
    }

    /**
     * [execute description]
     *
     * @param  \Magento\Framework\Event\Observer $observer [description]
     * @return [type]                                      [description]
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();

        $csvpriceData = $this->_csvpriceFactory->addCsvFilter($product->getId(), 0);
        if (!empty($csvpriceData)) {
            $csvprice = $csvpriceData['csv_price'];
            if (!empty($csvprice)) {
                $product->setCsvPrice($csvprice);
            } else {
                $product->setCsvPrice('');
            }
        } else {
            $product->setCsvPrice('');
        }
    }
}
