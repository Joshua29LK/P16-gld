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

namespace Itoris\ProductPriceFormula\Helper;

class Price extends Data
{

    public function getProductFinalPrice($item, $forReindex = false, $storeId = null, $customerGroupId = null) {
        if (!$this->getDataHelper()->isEnabled()) return;
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection =  $resource->getConnection('read');
        /** @var $item Mage_Sales_Model_Quote_Item */
        $productId = (int)$item->getProductId();
        if (!$productId) return null;
        $options = $forReindex ? null : $this->_getBuyRequest($item)->getOptions();
        $optionsQty = $forReindex ? [] : (array) $this->_getBuyRequest($item)->getOptionsQty();
        $tableCondition = $resource->getTableName('itoris_productpriceformula_conditions');
        $tableSettings = $resource->getTableName('itoris_productpriceformula_formula');
        $tableGroup = $resource->getTableName('itoris_productpriceformula_group');
        $conditionData = $connection->fetchAll("
            select {$tableSettings}.*, {$tableCondition}.*, group_concat({$tableGroup}.group_id) as group_id from {$tableCondition}
            join {$tableSettings} on {$tableCondition}.formula_id={$tableSettings}.formula_id
            and {$tableSettings}.product_id={$productId} and {$tableSettings}.status=1
            left join {$tableGroup} on {$tableCondition}.formula_id={$tableGroup}.formula_id
            group by {$tableCondition}.condition_id
            order by {$tableSettings}.position, {$tableCondition}.position
        ");
        $conditionPrice = [];
        if (!count($conditionData)) return;
        
        $storeId = $storeId === null ? (int)$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getStoreId() : $storeId;
        $product = $item->getProduct()->setStoreId($storeId)->load($productId);

        $optionData = [];
        if (is_array($options)) $optionData = $this->getOptionData($options, $optionsQty, $product);

        $dataBySku = (array) $this->getAttributeData($product);
        $price = $product->getFinalPrice((int)$item->getQty());
        $minPrice = $price;
        if ($product->getSpecialPrice()) {
            $minPrice = $product->getTierPrice($item->getQty()) ? min($product->getSpecialPrice(), $product->getTierPrice($item->getQty()), $product->getPrice()) : min($product->getSpecialPrice(), $product->getPrice());
        } else {
            $minPrice = $product->getTierPrice($item->getQty()) ? min($product->getTierPrice($item->getQty()), $product->getPrice()) : $product->getPrice();
        }
        //if ($minPrice && $minPrice < $price) $price = $minPrice;
        
        $dataBySku['{configured_price}'] = $price;
        $dataBySku['{initial_price}'] = $product->getPrice();
        $dataBySku['{special_price}'] = $product->getSpecialPrice();
        $dataBySku['{tier_price}'] = $product->getTierPrice($item->getQty());
        $customer = $this->_objectManager->get('Magento\Customer\Model\Session')->getCustomer();
        $dataBySku['{customer_id}'] = $customer ? (int)$customer->getId() : 0;
        
        $childs = null;
        if ($item->getProductType() == 'configurable') {
            foreach((array) $item->getBuyRequest()->getSuperAttribute() as $key => $value) {
                $attr = $this->_objectManager->get('Magento\Catalog\Model\ResourceModel\Eav\Attribute')->load($key);
                $label = $attr->getSource()->getOptionText($value);
                $dataBySku['{'.$attr->getAttributeCode().'}'] = $label;
            }
            $childs = $item->getChildren();
            if (count($childs)) {
                $dataBySku['{configurable_pid}'] = (int) $childs[0]->getProduct()->getId();
            } else $dataBySku['{configurable_pid}'] = 0;
        }
        
        $dataBySku['{qty}'] = $item->getQty();
        foreach($optionData as $key => $value) $dataBySku[$key] = $value;
        unset($dataBySku['{price}']);

        $productCurrentPrice = $price;
        $disallowCriteria = [];
        $variables = [];

        foreach ($conditionData as $value) {
            if ($value['store_ids'] && $storeId) {
                $storeIds = explode(',', $value['store_ids']);
                if (!in_array($storeId, $storeIds)) continue;
            }
            if (!$this->correctDate($value['active_from'], $value['active_to']) || !$this->customerGroup($value['group_id'], $customerGroupId)) continue;
            $variableString = str_replace(["\r","\n","\t"], '', $value['variables']);
            $variableString = str_replace('{price}', $productCurrentPrice, $variableString);
            $conditionString = $value['condition'];
            $priceString = $value['price'];
            $weightString = $value['weight'];
            $overrideWeight = (int)$value['override_weight'];
            $disallowCriteria[(int) $value['formula_id']] = (array) json_decode($value['disallow_criteria']);
            $qtyInResult = strrpos($value['price'], '{qty}') !== false ? (int) $dataBySku['{qty}'] : 1;
            foreach ($dataBySku as $sku => $valueOption) {
                if ($valueOption !== null && $valueOption !== '' && !is_array($valueOption)) {
                    if (!is_numeric($valueOption)) $valueOption = '"' . addslashes($valueOption) . '"';
            
                    // Check if $variableString is not null before calling str_ireplace
                    if ($variableString !== null) {
                        $variableString = str_ireplace($sku, $valueOption, $variableString);
                    }
            
                    // Check if $conditionString is not null before calling str_ireplace
                    if ($conditionString !== null) {
                        $conditionString = str_ireplace($sku, $valueOption, $conditionString);
                    }
            
                    // Check if $priceString is not null before calling str_ireplace
                    if ($priceString !== null) {
                        $priceString = str_ireplace($sku, $valueOption, $priceString);
                    }
            
                    if ($overrideWeight && $weightString !== null) {
                        $weightString = str_ireplace($sku, $valueOption, $weightString);
                    }
            
                    foreach ($disallowCriteria[(int) $value['formula_id']] as $key => $criteria) {
                        // Check if $criteria->formula is not null before calling str_ireplace
                        if ($criteria->formula !== null) {
                            $disallowCriteria[(int) $value['formula_id']][$key]->formula = str_ireplace($sku, $valueOption, $criteria->formula);
                        }
                    }
                }
            }
            //$variableString = str_ireplace('{price}', '@price', $variableString);

            // Check if $conditionString is not null before calling str_ireplace
            if ($conditionString !== null) {
                $conditionString = str_ireplace('{price}', '@price', $conditionString);
            }

            // Check if $priceString is not null before calling str_ireplace
            if ($priceString !== null) {
                $priceString = str_ireplace('{price}', '@price', $priceString);
            }

            // Check if $weightString is not null before calling str_ireplace
            if ($weightString !== null) {
                $weightString = str_ireplace('{price}', '@price', $weightString);
            }
            
            //JS -> PHP math constants conversion
            $map = ["E" => "M_E","LN2" => "M_LN2","LN10" => "M_LN10","LOG2E" => "M_LOG2E","LOG10E" => "M_LOG10E","PI" => "M_PI","SQRT1_2" => "M_SQRT1_2","SQRT2" => "M_SQRT2"];
            
            foreach(explode('var ', $variableString) as $variable) {
                if (!trim($variable)) continue; else $pos = strpos($variable, '=');
                if ($pos === false) continue;
                $varName = trim(substr($variable, 0, $pos));
                if (!$varName) continue; else $varStr = trim(substr($variable, $pos + 1));
                if (!$varStr) continue;
                
                foreach(array_keys($variables) as $key) $varStr = str_ireplace('{'.$key.'}', '@$_dpoVars["'.$key.'"]', $varStr);
                
                preg_match_all('/\{.*}/U', $varStr, $objectMatch);
                foreach($objectMatch[0] as $match) {
                    if (!$match) continue; else $ev = json_decode($match, true);
                    if ($ev && is_array($ev)) {
                        $rnd = rand();
                        $variables[$rnd] = $ev;
                        $varStr = str_ireplace($match, '@$_dpoVars["'.$rnd.'"]', $varStr);
                    }
                }
                
                
                $varStr = preg_replace('/\{(.*)}/U', 'false', $varStr);
                $varStr = $this->str_replace_outside_quotes(array_keys($map), array_values($map), $varStr);

                $varResult = 0;
                
                //$varResultFunc = @create_function('&$varResult, $_dpoVars', '$varResult = '.$varStr.';');
                //$varResultFunc($varResult, $variables);
                $_dpoVars = $variables;
                @eval('$varResult = '.$varStr.';');
                
                $variables[$varName] = $varResult;
            }
            
            foreach ($variables as $key => $var) {
                // Check if $conditionString is not null before calling str_ireplace
                if ($conditionString !== null) {
                    $conditionString = str_ireplace('{'.$key.'}', '@$_dpoVars["'.$key.'"]', $conditionString);
                }
            
                // Check if $priceString is not null before calling str_ireplace
                if ($priceString !== null) {
                    $priceString = str_ireplace('{'.$key.'}', '@$_dpoVars["'.$key.'"]', $priceString);
                }
            
                // Check if $weightString is not null before calling str_ireplace
                if ($weightString !== null) {
                    $weightString = str_ireplace('{'.$key.'}', '@$_dpoVars["'.$key.'"]', $weightString);
                }
            
                foreach ($disallowCriteria[(int) $value['formula_id']] as $key2 => $criteria) {
                    // Check if $criteria->formula is not null before calling str_ireplace
                    if ($criteria->formula !== null) {
                        $disallowCriteria[(int) $value['formula_id']][$key2]->formula = str_ireplace('{'.$key.'}', '@$_dpoVars["'.$key.'"]', $criteria->formula);
                    }
            
                    // Check if $criteria->message is not null before calling str_ireplace
                    if ($criteria->message !== null) {
                        $disallowCriteria[(int) $value['formula_id']][$key2]->message = str_ireplace('{'.$key.'}', '@$_dpoVars["'.$key.'"]', $criteria->message);
                    }
                }
            }
            
            $variableString = $variableString !== null ? preg_replace('/\{(.*)}/U', 'false', $variableString) : null;
            $conditionString = $conditionString !== null ? preg_replace('/\{(.*)}/U', 'false', $conditionString) : null;
            $priceString = $priceString !== null ? preg_replace('/\{(.*)}/U', '0', $priceString) : null;
            $weightString = $weightString !== null ? preg_replace('/\{(.*)}/U', '0', $weightString) : null;
            
            foreach ($disallowCriteria[(int) $value['formula_id']] as $key => $criteria) {
                // Check if $criteria->formula is not null before calling preg_replace
                $criteria->formula = $criteria->formula !== null ? preg_replace('/\{(.*)}/U', '0', $criteria->formula) : null;
            }                     
            
            $variableString = $this->str_replace_outside_quotes(array_keys($map), array_values($map), $variableString);
            $conditionString = $this->str_replace_outside_quotes(array_keys($map), array_values($map), $conditionString);
            $priceString = $this->str_replace_outside_quotes(array_keys($map), array_values($map), $priceString);
            $weightString = $this->str_replace_outside_quotes(array_keys($map), array_values($map), $weightString);
            foreach($disallowCriteria[(int) $value['formula_id']] as $key => $criteria) {
                $disallowCriteria[(int) $value['formula_id']][$key]->formula = $this->str_replace_outside_quotes(array_keys($map), array_values($map), $criteria->formula);
            }

            preg_match_all('/\{.*}/U', $conditionString ?? '', $resultCond);

            if (!array_key_exists($value['formula_id'], $conditionPrice)) {
                $conditionPrice[$value['formula_id']] = [];
            }
            
            $conditionPrice[$value['formula_id']][] = ['price' => $priceString, 'condition' => $conditionString, 'qty_in_result' => $qtyInResult, 'apply_to_total' => $value['apply_to_total'], 'override_weight' => $overrideWeight, 'weight' => $weightString];

        }
        $priceForCompare = 0; $apply_to_total = false; $weight = $item->getWeight();
        foreach ($conditionPrice as $formulaId => $values) {
            $isRightCondition = false;
        
            foreach ($values as $value) {
                if (!$isRightCondition) {
                    $condition = $value['condition'] ?? '';
                    $priceCond = $value['price'] ?? '';
        
                    $qtyInResult = $value['qty_in_result'];
                    $apply_to_total = (int)$value['apply_to_total'];
        
                    if ($condition !== false && $condition == '') {
                        $condition = true;
                    }
        
                    if ($condition != '' && $priceCond) {
                        $condition = str_ireplace('@price', $productCurrentPrice, $condition);
                        $priceCond = str_ireplace('@price', $productCurrentPrice, $priceCond);
        
                        if ($condition !== null && $priceCond !== null) {
                            $_dpoVars = $variables;
                            @eval('if (' . $condition . ') {$isRightCondition=true; $priceForCompare = (' . $priceCond . ');}');
        
                            if ($priceForCompare > 0) {
                                $productCurrentPrice = $priceForCompare;
                            }
        
                            if ($isRightCondition && $value['override_weight']) {
                                $weightCond = $value['weight'] ?? '';
                                $weightCond = str_ireplace('@price', $productCurrentPrice, $weightCond);
        
                                if ($weightCond !== null) {
                                    $_dpoVars = $variables;
                                    @eval('$weight = ' . $weightCond . ';');
                                }
                            }
                        }
                    }
                } else {
                    continue;
                }
            }
        }
        $finalPrice = $priceForCompare > 0 ? $priceForCompare / ($apply_to_total ? $item->getQty() : 1) : null;

        if (!$forReindex && $item->getQuote()) {
            $hasError = false;
            foreach($disallowCriteria as $formula) {
                foreach($formula as $criteria) {
                    //$validationFunc = @create_function('&$hasError, $_dpoVars', 'if (' . $criteria->formula . ') $hasError = true;');
                    //$validationFunc($hasError, $variables);
                    $_dpoVars = $variables;
                    @eval('if (' . $criteria->formula . ') $hasError = true;');
                    
                    if ($hasError) {
                        $item->setPriceFormulaError($criteria->message);
                        $item->getQuote()->setHasError(true);
                        break 2;
                    }
                }
            }
        }
        
        $item->setWeight($weight);
        if (is_array($childs)) {
            foreach($childs as $child) $child->setWeight($weight);
        }
        
        return $finalPrice;

    }

    public function _getBuyRequest($item) {
        $option = $item->getOptionByCode('info_buyRequest');
        if ($option) {
            $value = json_decode($option->getValue(), true); //in M2.2 json used for the buy request
            if (is_null($value)) $value = unserialize($option->getValue()); //in M<2.2 the buy request is serialized
        } else $value = [];

        $buyRequest = new \Magento\Framework\DataObject($value);

        // Overwrite standard buy request qty, because item qty could have changed since adding to quote
        $buyRequest->setOriginalQty($buyRequest->getQty())
            ->setQty($item->getQty() * 1);

        return $buyRequest;
    }

    protected function getOptionData($options, $optionsQty, $product) {
        $valueBySku = [];
        foreach ($options as $optionId => $optionValue) {
            $optionData = $product->getOptionById($optionId);
            if (is_array($optionValue) && $optionData && in_array($optionData->getType(), ['drop_down', 'radio', 'checkbox', 'multiple'])) {
                foreach ($optionValue as $subOptionId) {
                    if ($optionData->getValues()) {
                        foreach ($optionData->getValues() as $subOptionData) {
                            if ($subOptionData->getOptionTypeId() == (int)$subOptionId) {
                                $valueBySku['{' . $subOptionData->getSku() . '}'] = $subOptionData->getTitle();
                                $valueBySku['{' . $subOptionData->getSku() . '.qty}'] = isset($optionsQty[$optionId][$subOptionId]) ? (int) $optionsQty[$optionId][$subOptionId] : 0;
                                $valueBySku['{' . $subOptionData->getSku() . '.price}'] = $subOptionData->getPrice();
                                $valueBySku['{' . $subOptionData->getSku() . '.length}'] = strlen(trim($subOptionData->getTitle()));
                            }
                        }
                    }

                }
            } else if (is_object($optionData)) {
                if ($optionData->getValues() && in_array($optionData->getType(), ['drop_down', 'radio', 'checkbox', 'multiple'])) {
                    foreach ($optionData->getValues() as $subOptionData) {
                        if ($subOptionData->getOptionTypeId() == (int)$optionValue) {
                            $valueBySku['{' . $subOptionData->getSku() . '}'] = $subOptionData->getTitle();
                            $valueBySku['{' . $subOptionData->getSku() . '.qty}'] = isset($optionsQty[$optionId]) ? (int) $optionsQty[$optionId] : 0;
                            $valueBySku['{' . $subOptionData->getSku() . '.price}'] = $subOptionData->getPrice();
                             $valueBySku['{' . $subOptionData->getSku() . '.length}'] = strlen(trim($subOptionData->getTitle()));
                        }
                    }
                } else if (!is_null($optionData->getSku()) && $optionData->getSku()) {
                    $valueBySku['{' . $optionData->getSku() . '}'] = $optionValue;
                    $valueBySku['{' . $optionData->getSku() . '.qty}'] = 0;
                    $valueBySku['{' . $optionData->getSku() . '.price}'] = $optionData->getPrice();
                    $valueBySku['{' . $optionData->getSku() . '.length}'] = strlen(trim($optionValue));
                }
            }
        }
        return $valueBySku;
    }

    protected function getAttributeData($product) {
        $attributes = $product->getAttributes();
        $valueBySku = [];
        foreach ($attributes as $attribute) {
            try {
                $attributeCode = $attribute->getAttributeCode();
                $attributeCodeStr = '{' . $attributeCode . '}';
                $options = $attribute->getOptions();
                $value = $product->getData($attributeCode);
                if (!empty($options)) foreach($options as $option) {
                    if ((int)$option['value'] == (int)$value) {
                        $value = $option['label'];
                        break;
                    }
                }
                $valueBySku[$attributeCodeStr] = $value;
            } catch (\Exception $e) { }

        }
        return $valueBySku;
    }
    
    public function str_replace_outside_quotes($replace, $with, $string){
        if ($string !== null) {
            $result = "";
            $outside = preg_split('/("[^"]*"|\'[^\']*\')/', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
            while ($outside) $result .= str_replace($replace, $with, array_shift($outside)).array_shift($outside);
            return $result;
        } else {
            return null; // or handle null string case accordingly
        }
    }
    
    public function getDataHelper() {
        return $this->_objectManager->get('Itoris\ProductPriceFormula\Helper\Data');
    }
}