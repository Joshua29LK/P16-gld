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

namespace Itoris\ProductPriceFormula\Plugin;

class CatalogPricesIndex {
    public function aroundExecute($subject, $proceed, $ids = null) {
        $returnValue = $proceed($ids);
        try {
            $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->_res = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
            $this->_con =  $this->_res->getConnection('write');
            $this->_indexTable = $this->_res->getTableName('catalog_product_index_price');
            $this->_tableCondition = $this->_res->getTableName('itoris_productpriceformula_conditions');
            $this->_tableSettings = $this->_res->getTableName('itoris_productpriceformula_formula');
            $this->_tableGroup = $this->_res->getTableName('itoris_productpriceformula_group');
            if (!is_null($ids)) {
                if (is_array($ids)) {
                    foreach($ids as $id) $this->updateProductPriceIndex($id);
                } else $this->updateProductPriceIndex($ids);
            } else {
                $ids = $this->_con->fetchCol("select distinct `entity_id` from {$this->_indexTable}");
                foreach($ids as $id) $this->updateProductPriceIndex($id);
            }
        } catch (\Exception $e) { }
        return $returnValue;
    }
    
    public function updateProductPriceIndex($productId) {
        $conditionData = $this->_con->fetchAll("
            select {$this->_tableSettings}.*, {$this->_tableCondition}.*, group_concat({$this->_tableGroup}.group_id) as group_id from {$this->_tableCondition}
            join {$this->_tableSettings} on {$this->_tableCondition}.formula_id={$this->_tableSettings}.formula_id
            and {$this->_tableSettings}.product_id={$productId} and {$this->_tableSettings}.status=1
            left join {$this->_tableGroup} on {$this->_tableCondition}.formula_id={$this->_tableGroup}.formula_id
            group by {$this->_tableCondition}.condition_id
            order by {$this->_tableSettings}.position, {$this->_tableCondition}.position
        ");
        if (empty($conditionData)) return;
        $item = new \Magento\Framework\DataObject([
            'product_id' => $productId,
            'product' => $this->_objectManager->create('Magento\Catalog\Model\Product'),
            'qty' => 1
        ]);
        $groupIds = $this->_con->fetchCol("select `customer_group_id` from {$this->_indexTable} where `entity_id`={$productId}");
        foreach($groupIds as $groupId) {
            $formulaPrice = $this->_objectManager->get('Itoris\ProductPriceFormula\Helper\Price')->getProductFinalPrice($item, true, null, $groupId);
            if (!is_null($formulaPrice) && floatval($formulaPrice) > 0) {
                $finalPrice = floatval($formulaPrice);
                $this->_con->query("update {$this->_indexTable} set `price`={$finalPrice}, `final_price`={$finalPrice} where `entity_id`={$productId} and `customer_group_id`={$groupId}");
            }
        }
    }
}