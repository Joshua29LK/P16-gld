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

class ExportAll extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $res = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $con = $res->getConnection('read');
        $configs = $con->fetchAll("select * from {$res->getTableName('itoris_productpriceformula_formula')} order by `formula_id`");
        foreach($configs as &$config) {
            $config['product_sku'] =  $con->fetchOne("select `sku` from {$res->getTableName('catalog_product_entity')} where `entity_id` = ".$config['product_id']);
            $config['conditions'] = $con->fetchAll("select * from {$res->getTableName('itoris_productpriceformula_conditions')} where `formula_id`=".$config['formula_id']);
            $config['groups'] = $con->fetchAll("select * from {$res->getTableName('itoris_productpriceformula_group')} where `formula_id`=".$config['formula_id']);
            unset($config['formula_id']);
            unset($config['product_id']);
            foreach($config['conditions'] as &$condition) {
                unset($condition['formula_id']);
                unset($condition['condition_id']);
            }
            foreach($config['groups'] as &$group) unset($group['formula_id']);
        }
        $str = json_encode($configs);
        $this->getResponse()->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', 'application/octet-stream', true)
            ->setHeader('Content-Length', strlen($str), true)
            ->setHeader('Content-Disposition', 'attachment; filename="' . basename('PriceFormulaDump'.date('Y-m-d').'.json') . '"', true)
            ->setHeader('Last-Modified', date('r'), true)
            ->sendHeaders();
        $this->getResponse()->setBody($str);
    }
}