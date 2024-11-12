<?php
namespace Bss\SpecificatiesBox\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Itoris\ProductPriceFormula\Helper\Data;
use Magento\Tax\Model\Calculation as TaxCalculation;

/**
 * Class SpecificatiesBox
 *
 * Block class for displaying specifications and handling tax calculations.
 */
class SpecificatiesBox extends Template
{
    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var TaxCalculation
     */
    protected $taxCalculation;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param Registry $registry
     * @param Data $helper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param TaxCalculation $taxCalculation
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        TaxCalculation $taxCalculation,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_registry = $registry;
        $this->helper = $helper;
        $this->_objectManager = $objectManager;
        $this->taxCalculation = $taxCalculation;
    }


    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    public function getTaxRate()
    {
        $product = $this->getCurrentProduct();
        
        if (!$product) {
            return null;
        }
        
        $taxClassId = $product->getTaxClassId();
        
        $customerGroupId = $this->_objectManager->get('Magento\Customer\Model\Session')->getCustomerGroupId();
        $store = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore();
        
        $request = $this->taxCalculation->getRateRequest(null, null, $customerGroupId, $store);
        $request->setProductClassId($taxClassId);
        
        $taxRate = $this->taxCalculation->getRate($request);

        return $taxRate;
    }

    public function getConditions($productId) {
        $conditions = $this->prepareConditions($productId);
        return $conditions;
    }

    function getWidthHeightIds() {
        $currentProduct = $this->getCurrentProduct();
        $data = $this->getDataBySku($currentProduct);
        $widthId = isset($data['{w}']['id']) ? $data['{w}']['id'] : null;
        $heightId = isset($data['{h}']['id']) ? $data['{h}']['id'] : null;

        return [
            'width_id' => $widthId,
            'height_id' => $heightId,
        ];
    }

    public function prepareConditions($productId) {
        $conditionCollection = $this->_objectManager->create('Itoris\ProductPriceFormula\Model\Condition')->getCollection();
        $conditionCollection->getSelect()->join(['settings' => $this->_objectManager->get('Magento\Framework\App\ResourceConnection')->getTableName('itoris_productpriceformula_formula')],
            'settings.formula_id = main_table.formula_id and settings.status=1 and settings.product_id = ' . $productId,
            ['active_from' => 'settings.active_from', 'active_to' => 'settings.active_to', 'run_always' => 'settings.run_always',
                'apply_to_total' => 'settings.apply_to_total', 'frontend_total' => 'settings.frontend_total',
                'disallow_criteria' => 'settings.disallow_criteria', 'store_ids' => 'settings.store_ids', 'variables' => 'settings.variables']
        );
        $conditionCollection->getSelect()->order(['settings.position', 'main_table.position']);
        $formulas = [];
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection =  $resource->getConnection('read');
        $tableGroup = $resource->getTableName('itoris_productpriceformula_group');
        $storeId = (int)$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getStoreId();
        foreach ($conditionCollection as $model) {
            $formulaId = $model->getFormulaId();
            $conditionId = $model->getId();
            if ($model->getStoreIds() && $storeId) {
                $storeIds = explode(',', $model->getStoreIds());
                if (!in_array($storeId, $storeIds)) continue;
            }
            $groupIds = $connection->fetchAll("select group_id from {$tableGroup} where formula_id={$formulaId}");
            if (!array_key_exists($formulaId, $formulas)) {
                $formulas[$formulaId] = [
                    'formula_id' => $formulaId,
                    'active_from' => $model->getActiveFrom(),
                    'active_to'   => $model->getActiveTo(),
                    'run_always'  => $model->getRunAlways(),
                    'variables'  => $model->getVariables(),
                    'apply_to_total'  => $model->getApplyToTotal(),
                    'frontend_total'  => $model->getFrontendTotal(),
                    'disallow_criteria'  => (array) json_decode($model->getDisallowCriteria()),
                    'groups'      => $groupIds,
                    'conditions'  => []
                ];
            }
            if (!array_key_exists($conditionId, $formulas[$formulaId]['conditions'])) {
                $formulas[$formulaId]['conditions'][$conditionId] = [
                    'condition_id' => $conditionId,
                    'condition'    => $model->getCondition(),
                    'price'        => $model->getPrice(),
                    'weight'        => $model->getWeight()
                ];
            }
        }
        foreach ($formulas as $key => $condition) {
            $formulas[$key]['conditions'] = array_values($condition['conditions']);
        }
        $formulas = array_values($formulas);
        return $formulas;
    }

    public function getDataBySku($product) {
        $dataBySku = [];
        $allConditionPriceArray = [];
        $replaceVars = ['.qty}', '.price}', '.length}'];
        
        foreach ($this->getConditions($product->getId()) as $conditionData) {
            foreach ($conditionData['conditions'] as $value) {
                $conditionString = $value['condition'] ?? '';
                $priceString = $value['price'] ?? '';
                $weightString = $value['weight'] ?? '';

                preg_match_all('/\{.*}/U', str_replace($replaceVars, '}', $conditionString), $conditionMatch);
                $allConditionPriceArray[] = $conditionMatch[0];

                preg_match_all('/\{.*}/U', str_replace($replaceVars, '}', $priceString), $priceMatch);
                $allConditionPriceArray[] = $priceMatch[0];

                preg_match_all('/\{.*}/U', str_replace($replaceVars, '}', $weightString), $weightMatch);
                $allConditionPriceArray[] = $weightMatch[0];
            }

            foreach ($conditionData['disallow_criteria'] as $value) {
                $value = (array) $value;
                $formulaString = $value['formula'] ?? '';

                preg_match_all('/\{.*}/U', str_replace($replaceVars, '}', $formulaString), $conditionMatch);
                $allConditionPriceArray[] = $conditionMatch[0];
            }

            $variablesString = $conditionData['variables'] ?? '';
            preg_match_all('/\{.*}/U', str_replace($replaceVars, '}', $variablesString), $conditionMatch);
            $allConditionPriceArray[] = $conditionMatch[0];
        }

        $dataBySku = $this->prepareDataBySku($allConditionPriceArray, $product);
        return $dataBySku;
    }

    protected function prepareDataBySku($allConditionPriceArray, $product) {
        $dataBySku = [];
        $_vars = [];
        foreach ($allConditionPriceArray as $arr) {
            foreach ($arr as $val) if (!empty($val)) $_vars[$val] = 1;
        }

        $options = $product->getProductOptionsCollection();
        if (!empty($options)) {
            foreach ($options as $option) {
                $optionSku = '{' . $option->getSku() . '}';
                if (isset($_vars[$optionSku])) {
                    $dataBySku[$optionSku] = ['type' => $option->getType(), 'id' => $option->getId()];
                } else {
                    $values  = $option->getValues();
                    if (!empty($values)) {
                        foreach ($values as $value) {
                            $optionSku = '{' . $value->getSku() . '}';
                            if (isset($_vars[$optionSku])) {
                                $dataBySku[$optionSku] = ['type' => $option->getType(), 'id' => $option->getId()];
                            }
                        }
                    }
                }
            }
        }
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $attributeType = $attribute->getBackendType();
            $attributeCodeStr = '{' . $attributeCode . '}';
            if (isset($_vars[$attributeCodeStr]) && $attributeCodeStr != '{price}') {
                $options = $attribute->getOptions();
                $value = $product->getData($attributeCode);
                if (!empty($options)) foreach($options as $option) {
                    if ((int)$option['value'] == (int)$value) {
                        $value = $option['label'];
                        break;
                    }
                }
                $dataBySku[$attributeCodeStr] = ['value' => $value];
            }
        }

        return $dataBySku;
    }

    public function getOptions($product) {
        return $this->prepareOptions($product);
    }

    protected function prepareOptions($product) {
        $options = $product->getProductOptionsCollection();
        $productId = $product->getId();
        $optionsData = [];
        foreach ($options as $option) {
            $optionId = $option->getId();
            $values  = (array) $option->getValues();
            if (!array_key_exists($optionId, $optionsData)) {
                $optionsData[$optionId] = [
                    'sku'    => $option->getSku(),
                    'type'   => $option->getType(),
                    'id'     => $optionId,
                    'values' => [],
                    'price'    => (float) $option->getPrice()
                ];
            }
            foreach ($values as $value) {
                $valueId = $value->getId();
                if (!array_key_exists($valueId, $optionsData[$optionId]['values'])) {
                    $optionsData[$optionId]['values'][$valueId] = [
                        'sku' => $value->getSku(),
                        'id'  => $valueId,
                        'price'    => (float) $value->getPrice()
                    ];
                }
            }
        }
        $optionsData = array_values($optionsData);
        return $optionsData;
    }

    public function specialPrice($product) {
        return $product->getSpecialPrice();
    }

    public function getTierPrices() {
        $tierPrices = [];
        $product = $this->getCurrentProduct();
        $priceCurrency = $this->_objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface');
        $tierPricesList = $product->getPriceInfo()->getPrice('tier_price')->getTierPriceList();
        foreach ($tierPricesList as $tierPrice) {
            $tierPrices[] = ['qty' => $tierPrice['price_qty'], 'price' => (float) $tierPrice['website_price'] /*$priceCurrency->convert($tierPrice['website_price'])*/];
        }
        return $tierPrices;
    }
}
