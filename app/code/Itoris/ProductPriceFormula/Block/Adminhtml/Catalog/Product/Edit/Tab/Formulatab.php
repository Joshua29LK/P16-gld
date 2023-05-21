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
 
namespace Itoris\ProductPriceFormula\Block\Adminhtml\Catalog\Product\Edit\Tab;

class Formulatab extends \Magento\Backend\Block\Widget\Container implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_template = 'Itoris_ProductPriceFormula::product/edit/tab.phtml';

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->_context = $context;
        parent::__construct($context, $data);
    }
    
    public function canShowTab()
    {
        return $this->getDataHelper()->isEnabled();
    }

    public function isHidden()
    {
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection =  $resource->getConnection('read');
        $type = $connection->fetchOne("select `type_id` from {$resource->getTableName('catalog_product_entity')} where entity_id={$this->getCurrentProductId()}");
        return $type == 'bundle' || $type == 'grouped';
    }

    public function getTabLabel()
    {
        return $this->escapeHtml(__('Product Price Formula'));
    }
    
    public function getGroupCode()
    {
        return \Magento\Catalog\Block\Adminhtml\Product\Edit\Tabs::BASIC_TAB_GROUP_CODE;
    }
    
    public function getTabTitle()
    {
        return $this->escapeHtml(__('Product Price Formula'));
    }

    public function operatorsTable() {
        return [
            ['>', 'Left part is greater than right part', '{width} > 100'],
            ['<', 'Right part is greater than left part', '{year} < 2013'],
            ['==', 'Left part is equal to the right part', '{color} == "Red"'],
            ['!=', 'Left part is not equal to the right part', '{color} != "Red"'],
            ['>=', 'Left part greater or equal to the right part', '{weight} >= 50'],
            ['<=', 'Right part greater or equal to the left part', '{qty} <= 5'],
            ['&&', 'The "AND" operator', '{width} > 100 && {height} > 100'],
            ['||', 'The "OR" operator', '{year} < 2000 || {year} > 2013'],
            ['()', 'Sub condition', '( {a} > 10 || {b} > 10 ) && {c} > 10'],
            ['+', 'Addition', '{a} + {b} > 10'],
            ['-', 'Subtraction', '{a} - {b} > 10'],
            ['*', 'Multiplication', '{width} * {height} > 1000'],
            ['/', 'Division', '{a} < {b} / PI'],
        ];
    }
    
    public function operatorsTableForPrice() {
        return [
            ['()', 'Sub condition', '( {sku1} + {sku2} ) / PI'],
            ['+', 'Addition', '{sku1} + 10'],
            ['-', 'Subtraction', '{sku1} - 10'],
            ['*', 'Multiplication', '2 * PI * {sku_radius}'],
            ['/', 'Division', '{sku1} / 1.5'],
        ];
    }

    public function mathFunctions() {
        return [
            ['abs(x)', 'Returns the absolute value of x'],
            ['acos(x)', 'Returns the arccosine of x, in radians'],
            ['asin(x)', 'Returns the arcsine of x, in radians'],
            ['atan(x)', 'Returns the arctangent of x as a numeric value between -PI/2 and PI/2 radians'],
            ['atan2(y,x)', 'Returns the arctangent of the quotient of its arguments'],
            ['ceil(x)', 'Returns x, rounded upwards to the nearest integer'],
            ['cos(x)', 'Returns the cosine of x (x is in radians)'],
            ['exp(x)', 'Returns the value of Ex'],
            ['floor(x)', 'Returns x, rounded downwards to the nearest integer'],
            ['log(x)', 'Returns the natural logarithm (base E) of x'],
            ['max(x,y,z,...,n)', 'Returns the number with the highest value'],
            ['min(x,y,z,...,n)', 'Returns the number with the lowest value'],
            ['pow(x,y)', 'Returns the value of x to the power of y'],
            ['random()', 'Returns a random number between 0 and 1'],
            ['round(x)', 'Rounds x to the nearest integer'],
            ['sin(x)', 'Returns the sine of x (x is in radians)'],
            ['sqrt(x)', 'Returns the square root of x'],
            ['tan(x)', 'Returns the tangent of an angle'],
        ];
    }

    public function constantTable() {
        return [
            ['E', 'Returns Euler\'s number (approx. 2.718)'],
            ['LN2', 'Returns the natural logarithm of 2 (approx. 0.693)'],
            ['LN10', 'Returns the natural logarithm of 10 (approx. 2.302)'],
            ['LOG2E', 'Returns the base-2 logarithm of E (approx. 1.442)'],
            ['LOG10E', 'Returns the base-10 logarithm of E (approx. 0.434)'],
            ['PI', 'Returns PI (approx. 3.14)'],
            ['SQRT1_2', 'Returns the square root of 1/2 (approx. 0.707)'],
            ['SQRT2', 'Returns the square root of 2 (approx. 1.414)'],
        ];
    }

    public function variablesTable() {
        return [
            ['{configured_price}', 'Price after product options selected'],
            ['{initial_price}', 'Price before options selected'],
            ['{price}', 'Price after all calculations applied'],
            ['{special_price}', 'Special price configured in the product'],
            ['{tier_price}', 'Current tier price when quantity changed'],
            ['{attribute_code}', 'Any product attribute code enclosed into {}'],
            ['{option_sku}', 'Call any product option by its SKU enclosed into {}'],
            ['{option_sku.qty}', 'The quantity of sub-option if <a href="https://www.itoris.com/magento-2-custom-options.html">Dynamic Product Options</a> installed'],
            ['{option_sku.price}', 'Get the price of option by sku'],
            ['{option_sku.length}', 'Get the length of entered text'],
            ['{configurable_pid}', 'Returns the ID of currently selected product within the configurable product'],
            ['{qty}', 'Product quantity selected'],
            ['{customer_id}', 'Get the current customer ID (0 if not logged in)'],
        ];
    }
    
    public function getFormulaSettingsForLoad() {
        $formulaCollection = $this->_objectManager->get('Itoris\ProductPriceFormula\Model\Formula')->getCollection();
        $formulaCollection->addFieldToFilter('main_table.product_id', $this->getCurrentProductId());
        $formulaCollection->getSelect()->order('main_table.position DESC');
        $formulas = [];
        foreach ($formulaCollection as $model) {
            $formulas[] = [
                'formula_id'  => $model->getFormulaId(),
                'name'        => $model->getName(),
                'position'    => $model->getPosition(),
                'status'      => $model->getStatus(),
                'active_from' => $model->getActiveFrom(),
                'active_to'   => $model->getActiveTo(),
                'group_id'    => $model->getGroupId(),
                'store_ids'    => $model->getStoreIds(),
                'variables'        => $model->getVariables(),
                'run_always'  => $model->getRunAlways(),
                'apply_to_total'  => $model->getApplyToTotal(),
                'frontend_total'  => $model->getFrontendTotal(),
                'disallow_criteria'  => (array)json_decode($model->getDisallowCriteria())
            ];
        }

        return $formulas;
    }

    public function lastFormulaIdFromDb() {
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection =  $resource->getConnection('read');
        $table = $resource->getTableName('itoris_productpriceformula_formula');
        return $connection->fetchOne("select max(formula_id) from {$table}");
    }

    public function lastConditionIdFromDb() {
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection =  $resource->getConnection('read');
        $table = $resource->getTableName('itoris_productpriceformula_conditions');
        return $connection->fetchOne("select max(condition_id) from {$table}");
    }

    public function getConditionsForLoad() {
        $conditionCollection = $this->_objectManager->get('Itoris\ProductPriceFormula\Model\Condition')->getCollection();
        $conditionCollection->getSelect()->join(['formula' => $this->_objectManager->get('Magento\Framework\App\ResourceConnection')->getTableName('itoris_productpriceformula_formula')],
            'main_table.formula_id = formula.formula_id and formula.product_id = ' . $this->getCurrentProductId(), ['order' => 'formula.position', 'formula_id' => 'formula.formula_id']
        )->order('formula.position DESC');
        $conditions = [];
        foreach ($conditionCollection as $model) {
            if (array_key_exists($model->getFormulaId(), $conditions)) {
                $conditions[(int)$model->getFormulaId()][] = [
                    'condition_id'         => $model->getConditionId(),
                    'formula_id'           => $model->getFormulaId(),
                    'condition'            => $model->getCondition(),
                    'position'             => $model->getPosition(),
                    'price'                => $model->getPrice(),
                    'override_weight'   => (int)$model->getOverrideWeight(),
                    'weight'            => $model->getWeight()
                ];
            } else {
                $conditions[(int)$model->getFormulaId()] = [
                     [
                        'condition_id'         => $model->getConditionId(),
                        'formula_id'           => $model->getFormulaId(),
                        'condition'            => $model->getCondition(),
                        'position'             => $model->getPosition(),
                        'price'                => $model->getPrice(),
                        'override_weight'   => (int)$model->getOverrideWeight(),
                        'weight'            => $model->getWeight()
                     ]
                ];
            }

        }
        return $conditions;
    }
    
    public function getCurrentProductId() {
        return (int) $this->getRequest()->getParam('id', 0);
    }
    
    public function getStoreViews() {
        $storeViews = [];
        $websites = $this->_objectManager->create('Magento\Store\Model\ResourceModel\Website\Collection');
        foreach($websites as $website) {
            $storeViews[$website->getName()] = [];
            $stores = $website->getStores();
            foreach($stores as $store) {
                $storeViews[$website->getName()][$store->getId()] = $store->getName();
            }
        }
        return $storeViews;
    }
    
    public function getDataHelper() {
        return $this->_objectManager->get('Itoris\ProductPriceFormula\Helper\Data');
    }
}