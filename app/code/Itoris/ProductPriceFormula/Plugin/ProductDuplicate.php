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

class ProductDuplicate {
    
    public function afterCopy($subject, $result) {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $this->_objectManager->get('Magento\Framework\App\RequestInterface');
        
        if (!$this->getDataHelper()->isEnabled()) return $result; //skip
        
        $this->duplicateProduct((int)$request->getParam('id'), (int)$result->getId());

        return $result;
    }
    
    public function duplicateProduct($oldProductId, $newProductId) {
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection =  $resource->getConnection('write');
        $tableFormula = $resource->getTableName('itoris_productpriceformula_formula');
        $tableCondition = $resource->getTableName('itoris_productpriceformula_conditions');
        $tableGroup = $resource->getTableName('itoris_productpriceformula_group');
        $formulas = $connection->fetchCol("select `formula_id` from {$tableFormula} where `product_id` = {$oldProductId}");
        foreach($formulas as $formulaId) {
            $formula = $this->_objectManager->create('Itoris\ProductPriceFormula\Model\Formula')->load($formulaId);
            $formula->setId(null)->setProductId($newProductId)->save();
            $conditions = $connection->fetchCol("select `condition_id` from {$tableCondition} where `formula_id` = {$formulaId}");
            foreach($conditions as $conditionId) {
                $condition = $this->_objectManager->create('Itoris\ProductPriceFormula\Model\Condition')->load($conditionId);
                $condition->setId(null)->setFormulaId($formula->getId())->save();
            }
            $groups = $connection->fetchCol("select `group_id` from {$tableGroup} where `formula_id` = {$formulaId}");
            foreach($groups as $group) $connection->query("insert into {$tableGroup} set `formula_id` = {$formula->getId()}, `group_id`={$group}");
        }
    }
    
    public function getDataHelper() {
        return $this->_objectManager->get('Itoris\ProductPriceFormula\Helper\Data');
    }
}