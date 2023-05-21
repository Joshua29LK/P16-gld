<?php
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_M2_PRODUCT_PRICE_FORMULA
 * @copyright  Copyright (c) 2016 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */

namespace Itoris\ProductPriceFormula\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;

class UpdateTotals implements ObserverInterface
{
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;
        $this->_request = $request;
        $this->_priceCurrency = $priceCurrency;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $quoteItems = $observer->getEvent()->getQuote()->getAllItems();
        $total = $observer->getEvent()->getTotal();
        if (!$total->getSubtotal()) return;
        /*$compensation = 0;
        $isCatalogPriceInclTax = (int)$this->_objectManager->get('Magento\Backend\App\ConfigInterface')->getValue('tax/calculation/price_includes_tax');
        foreach($quoteItems as $item) {
            if($item->getBuyRequest()->getRule())   continue ;
            $rowTotal = $this->_priceCurrency->round($item->getPrice()) * $item->getQty();
            $finalPrice = $this->_objectManager->get('Itoris\ProductPriceFormula\Helper\Price')->getProductFinalPrice($item);
            if (is_null($finalPrice)) continue;
            if ($isCatalogPriceInclTax && $item->getTaxPercent()) $finalPrice = 100 * $finalPrice / ( 100 + $item->getTaxPercent());
            $newRowTotal = $this->_priceCurrency->round($finalPrice * $item->getQty());
            if ($newRowTotal && $rowTotal) {
                $_compensation = $newRowTotal - $rowTotal;
                if (abs($_compensation) < 1) $compensation += $_compensation;
            }
        }
        if (!$compensation) return;
        $toUpdate = ['subtotal', 'base_subtotal', 'subtotal_incl_tax', 'base_subtotal_total_incl_tax',
                    'base_subtotal_incl_tax', 'subtotal_with_discount', 'base_subtotal_with_discount',
                    'grand_total', 'base_grand_total'];
    
        foreach($toUpdate as $key) if ($total->getData($key)) $total->setData($key, $total->getData($key) + $compensation);*/
        
        $toUpdate = ['subtotal', 'base_subtotal', 'subtotal_incl_tax', 'base_subtotal_total_incl_tax',
                    'base_subtotal_incl_tax', 'subtotal_with_discount', 'base_subtotal_with_discount',
                    'grand_total', 'base_grand_total'];
    
        foreach($toUpdate as $key) if ($total->getData($key)) $total->setData($key, round($total->getData($key), 2));
        
    }

}