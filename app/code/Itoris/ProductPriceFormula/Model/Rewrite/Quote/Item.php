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

namespace Itoris\ProductPriceFormula\Model\Rewrite\Quote;

use Magento\Quote\Model\Quote;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Quote\Api\Data\CartItemInterface;

class Item extends \Magento\Quote\Model\Quote\Item {
    
    private $formulaPrice;
    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Sales\Model\Status\ListFactory $statusListFactory,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Quote\Model\Quote\Item\OptionFactory $itemOptionFactory,
        \Magento\Quote\Model\Quote\Item\Compare $quoteItemCompare,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $productRepository,
            $priceCurrency,
            $statusListFactory,
            $localeFormat,
            $itemOptionFactory,
            $quoteItemCompare,
            $stockRegistry,
            $resource = null,
            $resourceCollection,
            $data
        );
    }
    
    public function calcRowTotal() {
        $finalPrice = $this->getFormulaPrice();
        if (!is_null($finalPrice)) {
            $this->setPrice($this->priceCurrency->round($finalPrice))
                ->setBaseOriginalPrice($this->priceCurrency->round($finalPrice));

            $this->setRowTotal($this->priceCurrency->convert($finalPrice * $this->getQty()))
                ->setBaseRowTotal($this->priceCurrency->convert($finalPrice * $this->getQty()));
                
            return $this;
        }
        return parent::calcRowTotal();
    }
    
    public function getRowTotal(){
        $finalPrice = $this->getFormulaPrice();
        if (!is_null($finalPrice)) {
            if ($this->isCatalogPriceInclTax() && $this->getTaxPercent()) $finalPrice = 100 * $finalPrice / ( 100 + $this->getTaxPercent());
            return $this->priceCurrency->convert($finalPrice * $this->getQty());
        }
        return parent::getRowTotal();
    }
    
    public function getBaseRowTotal(){
        $finalPrice = $this->getFormulaPrice();
        if (!is_null($finalPrice)) {
            if ($this->isCatalogPriceInclTax() && $this->getTaxPercent()) $finalPrice = 100 * $finalPrice / ( 100 + $this->getTaxPercent());
            return $this->priceCurrency->convert($finalPrice * $this->getQty());
        }
        return parent::getBaseRowTotal();
    }
    
    public function getFormulaPrice(){
        if (!$this->formulaPrice) {
            $this->formulaPrice = $this->_objectManager->get('Itoris\ProductPriceFormula\Helper\Price')->getProductFinalPrice($this);
        }
        if ($this->getCustomPrice()) {
            return null;
        }
        return $this->formulaPrice;
    }
    
    public function isCatalogPriceInclTax(){
        return intval($this->_objectManager->get('Magento\Backend\App\ConfigInterface')->getValue('tax/calculation/price_includes_tax'));
                //&& intval($this->_objectManager->get('Magento\Backend\App\ConfigInterface')->getValue('tax/cart_display/subtotal')) == 1;
    }
    
}