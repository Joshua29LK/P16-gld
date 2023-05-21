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

namespace Itoris\ProductPriceFormula\Observer\Adminhtml;

use Magento\Framework\Event\ObserverInterface;

class SaveFormula implements ObserverInterface
{
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;
        $this->_request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        if (!$this->getDataHelper()->isEnabled()) return; //skip
        if ($this->_request->getParam('back') == 'duplicate') return; //skip
        /*if ($this->_request->getActionName() == 'duplicate' && (int)$this->_request->getParam('id') && (int)$observer->getEvent()->getDataObject()->getId()) {
            $this->duplicateProduct((int)$this->_request->getParam('id'), (int)$observer->getEvent()->getDataObject()->getId());
            return;
        }*/
        
        $product = $observer->getEvent()->getDataObject();
        $productId = $product->getId();
        $settingsParam = $this->_request->getParam('itoris_productpriceformula_settings');
        $conditionsParam = $this->_request->getParam('itoris_productpriceformula_conditions');
        if (is_array($settingsParam)) {
            ksort($settingsParam);
        }
        
        $formulaIdsNotForDelete = [];
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection =  $resource->getConnection('read');
        $tableFormula = $resource->getTableName('itoris_productpriceformula_formula');
        $tableCondition = $resource->getTableName('itoris_productpriceformula_conditions');
        
        if (!empty($settingsParam)) {
            foreach ($settingsParam as $param) if (!isset($param['formula_id_to_delete'])) {
                $conditionNotForDelete = [];
                $settingsModel = $this->_objectManager->create('Itoris\ProductPriceFormula\Model\Formula');

                $formulaIdDb = (int)$param['formula_id_db'];
                $settingsModel->load($formulaIdDb);
                $settingsModel->setName($param['name']);
                $settingsModel->setProductId((int)$productId);
                $settingsModel->setStatus((int)$param['status']);
                $settingsModel->setApplyToTotal((int)$param['apply_to_total']);
                if (isset($param['stores_serialized']) && $param['stores_serialized']) {
                    $settingsModel->setStoreIds($param['stores_serialized']);
                } else {
                    $settingsModel->setStoreIds(isset($param['stores']) && (!in_array('', (array)$param['stores'])) ? trim(implode(',', (array)$param['stores'])) : '');
                }
                $settingsModel->setFrontendTotal((int)$param['frontend_total']);
                $disallowCriteria = [];
                if (isset($param['disallow_formula'])) {
                    foreach($param['disallow_formula'] as $key => $formula) $disallowCriteria[] = ['formula' => $formula, 'message' => $param['disallow_message'][$key]];
                }
                $settingsModel->setDisallowCriteria(json_encode($disallowCriteria));
                if (isset($param['position'])) {
                    $settingsModel->setPosition((int)$param['position']);
                } else {
                    $settingsModel->setPosition(0);
                }
                if (isset($param['active_from']) && !empty($param['active_from'])) {
                    $prepareStartDate = explode('/', $param['active_from']);
                    $startDate = $prepareStartDate[2] . '-' . $prepareStartDate[0] . '-' . $prepareStartDate[1];
                    $settingsModel->setActiveFrom($startDate);
                } else {
                    $settingsModel->setActiveFrom(null);
                }
                if (isset($param['active_to']) && !empty($param['active_to'])) {
                    $prepareEndDate = explode('/', $param['active_to']);
                    $endDate = $prepareEndDate[2] . '-' . $prepareEndDate[0] . '-' . $prepareEndDate[1];
                    $settingsModel->setActiveTo($endDate);
                } else {
                    $settingsModel->setActiveTo(null);
                }
                if (isset($param['run_always'])) {
                    $settingsModel->setRunAlways((int)$param['run_always']);
                } else {
                    $settingsModel->setRunAlways(0);
                }
                $settingsModel->setVariables(trim($param['variables']));
                $settingsModel->save();
                $formulaId = (int)$settingsModel->getId();
                $formulaIdsNotForDelete[] = $formulaId;
                $tableGroup = $resource->getTableName('itoris_productpriceformula_group');
                $valueUserGroup = isset($param['group']) ? $param['group'] : [];
                if (isset($param['group_serialized']) && $param['group_serialized']) $valueUserGroup = explode(',', $param['group_serialized']); //fix for M2.1
                $connection->query("delete from {$tableGroup} where formula_id={$formulaId}");
                foreach ($valueUserGroup as $group) {
                    if ($group != '') {
                        $connection->query("insert into {$tableGroup} (formula_id, group_id) values ({$formulaId}, {$group})");
                    }
                }
                if (!empty($conditionsParam)) {
                    if ($formulaIdDb != $formulaId) {
                        $conditionsParamByFormulaId = $conditionsParam[$formulaIdDb];
                    } else {
                        $conditionsParamByFormulaId = $conditionsParam[$formulaId];
                    }

                    foreach ($conditionsParamByFormulaId as $conditionId => $conditionData) if (isset($conditionData['position'])) {
                        $conditionModel = $this->_objectManager->create('Itoris\ProductPriceFormula\Model\Condition');
                        $conditionModel->load($conditionId);
                        $conditionModel->setFormulaId($formulaId);
                        if (isset($conditionData['condition'])) {
                            $conditionModel->setCondition($conditionData['condition']);
                        } else {
                            $conditionModel->setCondition(null);
                        }
                        $conditionModel->setPrice(@$conditionData['price']);
                        $conditionModel->setPosition($conditionData['position']);
                        $conditionModel->setOverrideWeight(isset($conditionData['override_weight']) && trim(@$conditionData['weight']) != "" ? 1 : 0);
                        $conditionModel->setWeight(@$conditionData['weight']);
                        $conditionModel->save();
                        $conditionNotForDelete[] = $conditionModel->getId();
                    }
                }
                if (!empty($conditionNotForDelete)) {
                    $conditionNotForDelete = implode(',', $conditionNotForDelete);
                    $connection->query("delete from {$tableCondition} where `condition_id` not in ({$conditionNotForDelete}) and formula_id={$formulaId}");
                } else {
                    $connection->query("delete from {$tableCondition} where formula_id={$formulaId}");
                }
            }
            
            if (!empty($formulaIdsNotForDelete)) {
                $formulaIdsNotForDelete = implode(',', $formulaIdsNotForDelete);
                $connection->query("delete from {$tableFormula} where `formula_id` not in ({$formulaIdsNotForDelete}) and product_id={$productId}");
            } else {
                $connection->query("delete from {$tableFormula} where product_id={$productId}");
            }
        }

    }
    
    public function getDataHelper() {
        return $this->_objectManager->get('Itoris\ProductPriceFormula\Helper\Data');
    }
}