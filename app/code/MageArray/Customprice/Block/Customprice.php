<?php

namespace MageArray\Customprice\Block;

class Customprice extends \Magento\Framework\View\Element\Template
{
    public const VALAUE_ZERO = 0;
    public const VALAUE_ONE = 1;
    public const VALAUE_TWO = 2;
    public const VALAUE_THREE = 3;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \MageArray\Customprice\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $_localeFormat;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var $_productloader
     */
    protected $_productloader;

    /**
     * @var $_product
     */
    public $_product;

    /**
     * @var \MageArray\Customprice\Model\CsvpriceFactory
     */
    protected $_csvpriceFactory;

    /**
     * [__construct description]
     *
     * @param \Magento\Framework\View\Element\Template\Context    $context         [description]
     * @param \Magento\Framework\ObjectManagerInterface           $objectManager   [description]
     * @param \MageArray\Customprice\Helper\Data                  $dataHelper      [description]
     * @param \Magento\Framework\Registry                         $registry        [description]
     * @param \Magento\Framework\Locale\FormatInterface           $localeFormat    [description]
     * @param \Magento\Store\Model\StoreManagerInterface          $storeManager    [description]
     * @param \Magento\Catalog\Model\ProductFactory               $_productloader  [description]
     * @param \MageArray\Customprice\Model\ResourceModel\Csvprice $csvpriceFactory [description]
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \MageArray\Customprice\Helper\Data $dataHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \MageArray\Customprice\Model\ResourceModel\Csvprice $csvpriceFactory
    ) {
        parent::__construct($context);
        $this->_objectManager = $objectManager;
        $this->_dataHelper = $dataHelper;
        $this->_coreRegistry = $registry;
        $this->_localeFormat = $localeFormat;
        $this->_storeManager = $storeManager;
        $this->_csvpriceFactory = $csvpriceFactory;
        $this->_product = $_productloader
            ->create()
            ->load($this->_coreRegistry->registry('current_product')->getId());
    }

    /**
     * [getOtheroptions description]
     *
     * @return [type] [description]
     */
    public function getOtheroptions()
    {
        $options = $this->_product->getOptions();
        $types = ['drop_down', 'radio', 'checkbox', 'multiple'];
        $otheroptions = [];
        foreach ($options as $o) {
            if (in_array($o->getType(), $types)) {
                $optdata = [];
                foreach ($o->getValues() as $value) {
                    $optdata[$value->getOptionTypeId()] =
                        [
                            "type" => $value->getType(),
                            "title" => $value->getDefaultTitle(),
                            "price" => $value->getPrice(),
                            "price_type" => $value->getDefaultPriceType()
                        ];
                }
                if (isset($optdata) && !empty($optdata)) {
                    $otheroptions[$o->getOptionId()] = $optdata;
                }
            } else {
                $otheroptions[$o->getOptionId()] =
                    [
                        "type" => $o->getType(),
                        "title" => $o->getDefaultTitle(),
                        "price" => $o->getPrice(),
                        "price_type" => $o->getDefaultPriceType()
                    ];
            }
        }

        return $otheroptions;
    }

    /**
     * [checkCustomOptionLabel description]
     *
     * @return [type] [description]
     */
    public function checkCustomOptionLabel()
    {
        $active = $this->_dataHelper
            ->getStoreConfig('customprice/general/active');

        if ($active) {
            $checkOptionCsvPrice = $this->_product
            ->getData('apply_csv_type') == 'multiple' ? self::VALAUE_ONE : self::VALAUE_ZERO;
            $onedimensionalCsvPrice = $this->_product
            ->getData('apply_csv_type') == 'dimensional' ? self::VALAUE_ONE : self::VALAUE_ZERO;
            $optionFieldName = ($this->_product->getCsvCsvLabel() != '') ?
            strtolower($this->_product->getCsvCsvLabel()) : '';

            $optionsarray = [];
            $options = $this->_product->getOptions();
            foreach ($options as $o) {
                if ($o->getType() == 'field') {
                    $optionsarray[] = strtolower($o->getTitle());
                } elseif ($checkOptionCsvPrice == self::VALAUE_ONE && $o->getType() == 'radio' ||
                    $checkOptionCsvPrice == self::VALAUE_ONE && $o->getType() == 'drop_down') {
                    $optionsarray[] = strtolower($o->getTitle());
                } elseif ($onedimensionalCsvPrice == self::VALAUE_ONE && $o->getType() == 'field') {
                    $optionsarray[] = strtolower($o->getTitle());
                }
            }

            $xlabel = $this->_product->getRowLabels() ?
            strtolower($this->_product->getRowLabels()) :
            strtolower($this->_dataHelper->getStoreConfig('customprice/general/row_label'));

            $ylabel = $this->_product->getColumnLabels() ?
            strtolower($this->_product->getColumnLabels()) :
            strtolower($this->_dataHelper->getStoreConfig('customprice/general/column_label'));

            $csvPrice = $this->_product->getCsvPrice();
            if ($onedimensionalCsvPrice == self::VALAUE_ONE && in_array($xlabel, $optionsarray)) {
                return true;
            } else {
                if (in_array($xlabel, $optionsarray) && in_array($ylabel, $optionsarray) && !empty($csvPrice)) {
                    return true;
                } elseif (in_array($xlabel, $optionsarray)
                    && in_array($ylabel, $optionsarray)
                    && in_array($optionFieldName, $optionsarray)) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    /**
     * [getOptionFieldName description]
     *
     * @return [type] [description]
     */
    public function getOptionFieldName()
    {
        return trim(strtolower($this->_product->getCsvCsvLabel()));
    }

    /**
     * [getCsvOptionData description]
     *
     * @return [type] [description]
     */
    public function getCsvOptionData()
    {
        $optionFieldName = $this->getOptionFieldName();

        $final = [];
        $options = $this->_product->getOptions();
        foreach ($options as $o) {
            if ($optionFieldName == trim(strtolower($o->getTitle()))) {
                $enabledCSV = self::VALAUE_ONE;
                $csvoption = (int)$o->getOptionId();
                $csvoptiontype = $o->getType();
                foreach ($o->getValues() as $_value) {
                    $optionSkuId = strtolower(str_replace(" ", '', $_value->getSku()));
                    $optionId = $_value->getOptionTypeId();

                    $res = $this->_csvpriceFactory->addCsvFilter($this->_product->getId(), $optionSkuId);
                    $passargument = [];
                    if (!empty($res)) {
                        $csvPrice = json_decode($res['csv_price'], true);
                        $row = $this->getRowColData($csvPrice, 'row');
                        $col = $this->getRowColData($csvPrice, 'col');
                        $passargument['row'] = $row;
                        $passargument['col'] = $col;
                        $final[$optionId] = array_merge_recursive($csvPrice, $passargument);
                    }
                }

                $final['enabledCSV'] = self::VALAUE_ONE;
                $final['row_id'] = isset($row['id']) ? $row['id'] : '';
                $final['col_id'] = isset($col['id']) ? $col['id'] : '';
                $final['csvoption'] = $csvoption;
                $final['csvoptiontype'] = $csvoptiontype;
                break;
            }
        }
        return $final;
    }

    /**
     * [isOptionDisplayDropdown description]
     *
     * @return boolean [description]
     */
    public function isOptionDisplayDropdown()
    {
        $prdDisplayDropdown = $this->_product->getDisplayAsDropdown();
        if ($prdDisplayDropdown == self::VALAUE_TWO) {
            $prdDisplayDropdown = $this->_dataHelper->getStoreConfig('customprice/general/displaydropdown');
        }
        return $prdDisplayDropdown;
    }

    /**
     * [isDisplayAllOption description]
     *
     * @return boolean [description]
     */
    public function isDisplayAllOption()
    {
        $isDisplayAllOption = $this->_product->getCsvDisplayAllDropdown();
        if ($isDisplayAllOption == self::VALAUE_TWO) {
            $isDisplayAllOption = $this->_dataHelper->getStoreConfig('customprice/general/range');
        }
        return $isDisplayAllOption;
    }

    /**
     * [getPercent description]
     *
     * @return [type] [description]
     */
    public function getPercent()
    {
        $taxHelper = $this->_objectManager->create(\Magento\Tax\Helper\Data::class);
        $priceDisplayType = $taxHelper->getPriceDisplayType($this->_storeManager->getStore());
        $percent = self::VALAUE_ZERO;
        if ($priceDisplayType == self::VALAUE_TWO || $priceDisplayType == self::VALAUE_THREE) {
            $taxCalculation = $this->_objectManager->create(\Magento\Tax\Api\TaxCalculationInterface::class);
            $currentCustomer = $this->_objectManager->create(\Magento\Customer\Model\Session::class);
            $taxClassId = $this->_product->getTaxClassId();
            $customerId = $currentCustomer->getCustomer()->getId();
            $percent = $taxCalculation
              ->getCalculatedRate($taxClassId, $customerId, $this->_storeManager->getStore());
        }
        return $percent;
    }

    /**
     * [getUnit description]
     *
     * @return [type] [description]
     */
    public function getUnit()
    {
        $unit = $this->_product->getCsvPriceUnit();
        if (empty($unit)) {
            $unit = $this->_dataHelper->getStoreConfig('customprice/general/unit');
        }
        return $unit;
    }

    /**
     * [getPriceMin description]
     *
     * @return [type] [description]
     */
    public function getPriceMin()
    {
        $prdPricemin = (int)$this->_product->getPricemin();
        if ($prdPricemin == self::VALAUE_TWO) {
            $prdPricemin = (int)$this->_dataHelper->getStoreConfig('customprice/general/pricemin');
        }
        return $prdPricemin;
    }

    /**
     * [getMaxError description]
     *
     * @return [type] [description]
     */
    public function getMaxError()
    {
        $maxError = $this->_product->getMaxError();
        if (empty($maxError)) {
            $maxError = $this->_dataHelper->getStoreConfig('customprice/general/max_error');
        }
        return $maxError;
    }

    /**
     * [getMinError description]
     *
     * @return [type] [description]
     */
    public function getMinError()
    {
        $minError = $this->_product->getMinError();
        if (empty($minError)) {
            $minError = $this->_dataHelper->getStoreConfig('customprice/general/min_error');
        }
        return $minError;
    }

    /**
     * [getNotFoundSizeMsg description]
     *
     * @return [type] [description]
     */
    public function getNotFoundSizeMsg()
    {
        $notFoundSizeError = $this->_product->getNotFoundSizeMsg();
        if (empty($notFoundSizeError)) {
            $notFoundSizeError = $this->_dataHelper->getStoreConfig('customprice/general/not_found_size');
        }
        return $notFoundSizeError;
    }

    /**
     * [getAlert description]
     *
     * @return [type] [description]
     */
    public function getAlert()
    {
        $error['max'] = __($this->getMaxError());
        $error['min'] = __($this->getMinError());
        $error['notfound'] = __($this->getNotFoundSizeMsg());
        return $error;
    }

    /**
     * [isIncludeBaseprice description]
     *
     * @return boolean [description]
     */
    public function isIncludeBaseprice()
    {
        $includebaseprice = $this->_product->getIncludeBaseprice();
        if ($includebaseprice == self::VALAUE_TWO) {
            $includebaseprice = $this->_dataHelper->getStoreConfig('customprice/general/include_baseprice');
        }
        return $includebaseprice;
    }

    /**
     * [getMaxMinDefaultDisplay description]
     *
     * @return [type] [description]
     */
    public function getMaxMinDefaultDisplay()
    {
        $maxmindefaultdisplay = $this->_product->getMaxMinDefaultDisplay();
        if ($maxmindefaultdisplay == self::VALAUE_TWO) {
            $maxmindefaultdisplay = $this->_dataHelper
            ->getStoreConfig('customprice/general/display_min_max_value_default');
        }
        return $maxmindefaultdisplay;
    }

    /**
     * [getRowColData description]
     *
     * @param  [type] $csvPrice [description]
     * @param  [type] $type     [description]
     * @return [type]           [description]
     */
    public function getRowColData($csvPrice, $type)
    {
        if (empty($csvPrice)) {
            return [];
        }
        $row = [];
        $col = [];
        $xlabel = $this->getRowLabels();
        $ylabel = $this->getColumnLabels();
        $options = $this->_product->getOptions();
        foreach ($options as $o) {
            $title = strtolower($o->getTitle());
            if ($o->getType() == 'field' && $title == $xlabel) {
                $row['type'] = $o->getType();
                $row['id'] = $o->getOptionId();
            }
            if ($o->getType() == 'field' && $title == $ylabel) {
                $col['type'] = $o->getType();
                $col['id'] = $o->getOptionId();
            }
        }

        if ($type == 'row') {
            $row['min'] = $csvPrice['minmax']['minRow'];
            $row['max'] = $csvPrice['minmax']['maxRow'];
            return $row;
        } else {
            $col['min'] = $csvPrice['minmax']['minCol'];
            $col['max'] = $csvPrice['minmax']['maxCol'];
            return $col;
        }
    }

    /**
     * [getRowLabels description]
     *
     * @return [type] [description]
     */
    public function getRowLabels()
    {
        $prdRowLabel = ($this->_product->getRowLabels() != '') ? strtolower($this->_product->getRowLabels()) : '';
        if ($prdRowLabel == '') {
            $prdRowLabel = strtolower($this->_dataHelper->getStoreConfig('customprice/general/row_label'));
        }
        return $prdRowLabel;
    }

    /**
     * [getColumnLabels description]
     *
     * @return [type] [description]
     */
    public function getColumnLabels()
    {
        $prdColumnLabel = ($this->_product->getColumnLabels() != '') ?
        strtolower($this->_product->getColumnLabels()) : '';
        if ($prdColumnLabel == '') {
            $prdColumnLabel = strtolower($this->_dataHelper->getStoreConfig('customprice/general/column_label'));
        }
        return $prdColumnLabel;
    }

    /**
     * [isActiveCsvPricing description]
     *
     * @return boolean [description]
     */
    public function isActiveCsvPricing()
    {
        if ($this->_product->getApplyCsvType()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * [jsonEncodeandDecode description]
     *
     * @return [type] [description]
     */
    public function jsonEncodeandDecode()
    {
        $passArgument = [];
        $csvPriceArr = json_decode($this->_product->getCsvPrice(), true);

        $newCsvPrice = [];

        if ($this->_product->getApplyCsvType() == 'multiple') {
            $passArgument = $this->getCsvOptionData();
            foreach ($passArgument as $key => $rows) {
                if (isset($rows['pricesheet'])) {
                    foreach ($rows['pricesheet'] as $rkey => $rowinner) {
                        foreach ($rowinner as $fkey => $row) {
                            $newPrice = $this->_dataHelper->convertPriceFromBaseCurrencyToCurrentCurrency($row);
                            $passArgument[$key]['pricesheet'][$rkey][$fkey] = round($newPrice, 3);
                        }
                        
                    }
                }
            }
        } elseif ($this->_product->getApplyCsvType() == 'dimensional') {
            foreach ($csvPriceArr['pricesheet'] as $key => $rows) {
                $newPrice = $this->_dataHelper->convertPriceFromBaseCurrencyToCurrentCurrency($rows);
                $newCsvPrice['pricesheet'][$key] = round($newPrice, 3);
            }
            $passArgument['row'] = $this->getRowColData($csvPriceArr, 'row');
        } elseif ($this->_product->getApplyCsvType() == 'simple') {
            foreach ($csvPriceArr['pricesheet'] as $key => $rows) {
                foreach ($rows as $rkey => $row) {
                    $newPrice = $this->_dataHelper->convertPriceFromBaseCurrencyToCurrentCurrency($row);
                    $newCsvPrice['pricesheet'][$key][$rkey] = round($newPrice, 3);
                }
            }
            $passArgument['row'] = $this->getRowColData($csvPriceArr, 'row');
            $passArgument['col'] = $this->getRowColData($csvPriceArr, 'col');
        }

        $newCsvPrice['minmax'] = $csvPriceArr['minmax'];
        $newCsvPrice['minmax']['minPrice'] = round(
            $this->_dataHelper->convertPriceFromBaseCurrencyToCurrentCurrency($csvPriceArr['minmax']['minPrice']),
            3
        );
        $newCsvPrice['minmax']['maxPrice'] = round(
            $this->_dataHelper->convertPriceFromBaseCurrencyToCurrentCurrency($csvPriceArr['minmax']['maxPrice']),
            3
        );
        $csvPrice = array_merge($csvPriceArr, $newCsvPrice);

        $passArgument['select'] = $this->isOptionDisplayDropdown();
        $passArgument['alldropdown'] = $this->isDisplayAllOption();
        $passArgument['showSelectSize'] = self::VALAUE_ONE;
        $passArgument['unit'] = $this->getUnit();
        $passArgument['pricemin'] = $this->getPriceMin();
        $passArgument['alert'] = $this->getAlert();
        $passArgument['step'] = self::VALAUE_ONE;
        $passArgument['price'] = $this->_product->getPrice();
        $passArgument['specialprice'] = $this->_product
        ->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
        $passArgument['includebaseprice'] = $this->isIncludeBaseprice();
        $passArgument['otheroptions'] = $this->getOtheroptions();
        $passArgument['markuptype'] = $this->_product->getCsvPriceMarkupType();
        $passArgument['markupvalue'] = $this->_product->getCsvPriceMarkupValue();
        $passArgument['csv-option'] = self::VALAUE_ZERO;
        $passArgument['maxMinError'] = $this->getMaxMinDefaultDisplay();
        $passArgument['productId'] = $this->_product->getId();
        $passArgument['productPriceFormat'] = $this->_localeFormat->getPriceFormat();
        $percent = $this->getPercent();
        $passArgument['taxrate'] = $percent > self::VALAUE_ZERO ? $percent : '';

        if (isset($passArgument['enabledCSV']) && $passArgument['enabledCSV'] == self::VALAUE_ONE) {
            $concat = $passArgument;
        } elseif (!empty($csvPrice)) {
            $concat = array_merge_recursive($csvPrice, $passArgument);
        } else {
            $concat = $passArgument;
        }

        return json_encode($concat);
    }

    /**
     * [getItemQty description]
     *
     * @return [type] [description]
     */
    public function getItemQty()
    {
        $itemId = $this->getRequest()->getParam('id');
        if ($itemId) {
            $item = $this->_objectManager->get(\Magento\Quote\Model\Quote\Item::class)->load($itemId);
            return $item->getQty();
        }
    }

    /**
     * [getConfigureMode description]
     *
     * @return [type] [description]
     */
    public function getConfigureMode()
    {
        return $this->getRequest()->getActionName();
    }
}
