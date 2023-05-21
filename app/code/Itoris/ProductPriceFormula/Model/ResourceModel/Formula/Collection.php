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

namespace Itoris\ProductPriceFormula\Model\ResourceModel\Formula;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected function _construct() {
        $this->_init('Itoris\ProductPriceFormula\Model\Formula', 'Itoris\ProductPriceFormula\Model\ResourceModel\Formula');
        $this->tableGroup = $this->getTable('itoris_productpriceformula_group');
        $this->tableFormula = $this->getTable('itoris_productpriceformula_formula');
    }

    protected function _initSelect() {
        parent::_initSelect();
        $this->getSelect()->joinLeft(
            ['group' => $this->tableGroup],
            'group.formula_id = main_table.formula_id',
            ['group_id' => 'group_concat(distinct group.group_id)']
        )->group('main_table.formula_id');
        return $this;
    }

    public function addGroupFilter($groupId) {
        $this->_select->having("group_id IS NULL OR FIND_IN_SET('" . intval($groupId) . "', group_id)");
        return $this;
    }
}