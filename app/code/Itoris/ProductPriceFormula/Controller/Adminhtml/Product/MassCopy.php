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
 * @package    ITORIS_M2_DYNAMIC_PRODUCT_OPTIONS
 * @copyright  Copyright (c) 2016 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */

namespace Itoris\ProductPriceFormula\Controller\Adminhtml\Product;

class MassCopy extends \Magento\Framework\App\Action\Action
{
    /**
     * @return mixed
     */
    public function execute()
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $filter = $this->_objectManager->create('Magento\Ui\Component\MassAction\Filter');
        $collectionFactory = $this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
        $collection = $filter->getCollection($collectionFactory->create());
        $productIds = $collection->getAllIds();
        
        $fromProductId = $this->getRequest()->getParam('from_product_id');
        if (is_array($productIds)) {
            $res = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
            $con = $res->getConnection('read');
            $configs = $con->fetchAll("select * from {$res->getTableName('itoris_productpriceformula_formula')} where `product_id`={$fromProductId} order by `formula_id`");
            if (!empty($configs)) {
                $saved = 0;
                foreach ($productIds as $newProductId) {
                    $con->query("delete from {$res->getTableName('itoris_productpriceformula_formula')} where `product_id`={$newProductId}"); //clean up
                    foreach($configs as $config) {
                        //copying formulas
                        $con->query("insert into {$res->getTableName('itoris_productpriceformula_formula')} set ".$this->getQueryString($con, array_merge($config, ['product_id' => $newProductId]), 'formula_id'));
                        $newFormulaId = (int) $con->fetchOne("select max(`formula_id`) from {$res->getTableName('itoris_productpriceformula_formula')}");
                        //copying conditions
                        $conditions = $con->fetchAll("select * from {$res->getTableName('itoris_productpriceformula_conditions')} where `formula_id`={$config['formula_id']} order by `condition_id`");
                        foreach($conditions as $condition) $con->query("insert into {$res->getTableName('itoris_productpriceformula_conditions')} set ".$this->getQueryString($con, array_merge($condition, ['formula_id' => $newFormulaId]), 'condition_id'));
                        //copying groups
                        $groups = $con->fetchAll("select * from {$res->getTableName('itoris_productpriceformula_group')} where `formula_id`={$config['formula_id']}");
                        foreach($groups as $group) $con->query("insert into {$res->getTableName('itoris_productpriceformula_group')} set ".$this->getQueryString($con, array_merge($group, ['formula_id' => $newFormulaId])));
                    }
                    $saved++;
                }
                $this->messageManager->addSuccess(__('%1 products have been changed', $saved));
            } else {
                $this->messageManager->addError(__('The product has no price formulas'));
            }
        } else {
            $this->messageManager->addError(__('Please select product ids'));
        }

        $this->_redirect('catalog/product/', ['_current' => true]);
    }
    
    public function getQueryString($con, $array, $primary = '') {
        $pairs = [];
        if ($primary) unset($array[$primary]); //do not include primary column
        foreach($array as $key => $value) $pairs[] = "`$key`=".(!is_null($value) ? $con->quote($value) : 'NULL');
        return implode(',', $pairs);
    }
}