<?php

namespace MageArray\Customprice\Plugin;

use Magento\Catalog\Model\Product;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class PricePlugin
{
    public const VALAUE_ZERO = 0;
    public const VALAUE_ONE = 1;
    public const VALAUE_TWO = 2;
    public const VALAUE_ONETHOUSAND = 1000;

    /**
     * @var \MageArray\Customprice\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \MageArray\Customprice\Model\CsvpriceFactory
     */
    protected $_csvpriceFactory;

    /**
     * [__construct description]
     *
     * @param \Magento\Framework\Event\ManagerInterface                  $eventManager     [description]
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface       $localeDate       [description]
     * @param PriceCurrencyInterface                                     $priceCurrency    [description]
     * @param GroupManagementInterface                                   $groupManagement  [description]
     * @param \Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory $tierPriceFactory [description]
     * @param \Magento\Framework\App\Config\ScopeConfigInterface         $config           [description]
     * @param \MageArray\Customprice\Helper\Data                         $dataHelper       [description]
     * @param \MageArray\Customprice\Model\ResourceModel\Csvprice        $csvpriceFactory  [description]
     * @param \Magento\Framework\ObjectManagerInterface                  $objectManager    [description]
     * @param \Magento\Catalog\Model\ProductFactory                      $productFactory   [description]
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        PriceCurrencyInterface $priceCurrency,
        GroupManagementInterface $groupManagement,
        \Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory $tierPriceFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \MageArray\Customprice\Helper\Data $dataHelper,
        \MageArray\Customprice\Model\ResourceModel\Csvprice $csvpriceFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->_eventManager = $eventManager;
        $this->_localeDate = $localeDate;
        $this->priceCurrency = $priceCurrency;
        $this->_groupManagement = $groupManagement;
        $this->tierPriceFactory = $tierPriceFactory;
        $this->config = $config;
        $this->_dataHelper = $dataHelper;
        $this->_objectManager = $objectManager;
        $this->_csvpriceFactory = $csvpriceFactory;
        $this->productFactory = $productFactory;
    }

    /**
     * [aroundGetFinalPrice description]
     *
     * @param  \Magento\Catalog\Model\Product\Type\Price $subject [description]
     * @param  callable                                  $proceed [description]
     * @param  [type]                                    $qty     [description]
     * @param  [type]                                    $product [description]
     * @return [type]                                             [description]
     */
    public function aroundGetFinalPrice(
        \Magento\Catalog\Model\Product\Type\Price $subject,
        callable $proceed,
        $qty,
        $product
    ) {
        $mainObject = $subject;
        $finalPrice = $proceed($qty, $product);
        $loadProduct = $this->productFactory->create()->load($product->getId());
        if ($this->_dataHelper->getStoreConfig('customprice/general/active') == self::VALAUE_ONE &&
            $loadProduct->getApplyCsvType()
        ) {
            $finalPrice = $this->_applyCustomPrice($product, $finalPrice);
        }
        return $finalPrice;
    }

    /**
     * [_applyCustomPrice description]
     *
     * @param  [type] $product    [description]
     * @param  [type] $finalPrice [description]
     * @return [type]             [description]
     */
    public function _applyCustomPrice($product, $finalPrice)
    {
        $mproduct = $this->productFactory->create()->load($product->getId());
        $inclbaseprice = $mproduct->getIncludeBaseprice() != self::VALAUE_TWO ?
            $mproduct->getIncludeBaseprice() :
            $this->_dataHelper->getStoreConfig('customprice/general/include_baseprice');
        $pricelevel = $mproduct->getPricemin() != self::VALAUE_TWO ?
            $mproduct->getPricemin() :
            $this->_dataHelper->getStoreConfig('customprice/general/pricemin');
        $xlabel = $mproduct->getRowLabels() != '' ?
            strtolower($mproduct->getRowLabels()) :
            strtolower($this->_dataHelper->getStoreConfig('customprice/general/row_label'));
        $ylabel = $mproduct->getColumnLabels() != '' ? strtolower($mproduct->getColumnLabels()) :
            strtolower($this->_dataHelper->getStoreConfig('customprice/general/column_label'));
        $onedimensionalCsvPrice = $mproduct->getIsOneDimensional();

        $basePrice = $product->getPrice();
        $sheetPrice = self::VALAUE_ZERO;

        if ($optionIds = $product->getCustomOption('option_ids')) {
            $optionPercent = [];
            $types = ['drop_down', 'radio', 'checkbox', 'multiple'];
            if ($mproduct->getApplyCsvType() == 'dimensional') {
                list($optionPercent, $row) = $this->getOptionPercent($product, $optionIds, $xlabel);
                $csvfile = $mproduct->getCsvPrice();
                if ($row && !empty($csvfile)) {
                    $csvPrice = json_decode($csvfile, true);

                    $confItemOption = $product->getCustomOption('option_' . $row);
                    $rowvalue = $confItemOption->getValue();

                    $pricesheet = $csvPrice['vals'];
                    $pricesheetRows = $pricesheet['row'];

                    $finalRowIndex = $this->getPriceLevel($pricelevel, $pricesheetRows, $rowvalue);
                    $sheetPrice = $csvPrice['pricesheet'][$finalRowIndex];
                }
            } else {
                list($optionPercent, $row, $col, $csvpriceperoption) = $this->getOptionPercent(
                    $product,
                    $optionIds,
                    $xlabel,
                    $ylabel,
                    $mproduct->getCsvCsvLabel()
                );

                $csvfile = $mproduct->getCsvPrice();

                if ($row && $col && !empty($csvfile) || $csvpriceperoption && $row && $col) {
                    $csvPrice = json_decode($csvfile, true);
                    if ($csvpriceperoption != '' && $mproduct->getApplyCsvType() == 'multiple') {
                        $csvPrice = $this->getCsvPriceValues($product, $mproduct, $csvpriceperoption);
                    }

                    $confItemOption = $product->getCustomOption('option_' . $row);
                    $rowvalue = $confItemOption->getValue();

                    $confItemOption = $product->getCustomOption('option_' . $col);
                    $columnvalue = $confItemOption->getValue();

                    $pricesheet = $csvPrice['vals'];

                    $pricesheetRows = $pricesheet['row'];
                    $pricesheetCols = $pricesheet['col'];

                    $finalRowIndex = $this->getPriceLevel($pricelevel, $pricesheetRows, $rowvalue);
                    $finalColumnIndex = $this->getPriceLevel($pricelevel, $pricesheetCols, $columnvalue);
                    $sheetPrice = $csvPrice['pricesheet'][$finalRowIndex][$finalColumnIndex];
                }
            }

            $sheetPrice = $this->getAddMarkupPrice($mproduct, $sheetPrice);
            if ($inclbaseprice) {
                $finalPrice = $finalPrice + $sheetPrice;
                $sheetPrice = $basePrice + $sheetPrice;
            } else {
                $finalPrice = $finalPrice - $basePrice;
                $finalPrice += $sheetPrice;
            }

            $finalPrice = $this->getPercentOptionPrice($product, $optionPercent, $finalPrice, $sheetPrice);
        }
        return $finalPrice;
    }

    /**
     * [getCsvPriceValues description]
     *
     * @param  [type] $product           [description]
     * @param  [type] $mproduct          [description]
     * @param  [type] $csvpriceperoption [description]
     * @return [type]                    [description]
     */
    public function getCsvPriceValues($product, $mproduct, $csvpriceperoption)
    {
        $confItemOption = $product->getCustomOption('option_' . $csvpriceperoption);
        $optionvalue = $confItemOption->getValue();
        $csvPrice = [];
        foreach ($mproduct->getOptions() as $o) {
            if ($csvpriceperoption == $o->getOptionId()) {
                $values = $o->getValues();
                foreach ($values as $k => $v) {
                    if ($optionvalue == $v->getOptionTypeId()) {
                        $optsku = strtolower(str_replace(' ', '', $v->getSku()));
                    }
                }
            }
        }

        $res = $this->_csvpriceFactory->addCsvFilter($product->getId(), $optsku);
        if (!empty($res)) {
            $csvPrice = json_decode($res['csv_price'], true);
        }
        return $csvPrice;
    }

    /**
     * [getOptionPercent description]
     *
     * @param  [type] $product        [description]
     * @param  [type] $optionIds      [description]
     * @param  string $xlabel         [description]
     * @param  string $ylabel         [description]
     * @param  string $csvoptionlabel [description]
     * @return [type]                 [description]
     */
    public function getOptionPercent(
        $product,
        $optionIds,
        $xlabel = '',
        $ylabel = '',
        $csvoptionlabel = ''
    ) {
        $optionPercent = [];
        $types = ['drop_down', 'radio', 'checkbox', 'multiple'];
        $row = false;
        $col = '';
        $csvpriceperoption = '';
        foreach (explode(',', $optionIds->getValue()) as $optionId) {
            if ($option = $product->getOptionById($optionId)) {
                $title = ($option->getTitle() != '') ? strtolower($option->getTitle()) : '';
                if ($title == $xlabel) {
                    $row = $option->getId();
                }
                if ($title == $ylabel) {
                    $col = $option->getId();
                }
                if ($csvoptionlabel != '' && strtolower($title) == strtolower($csvoptionlabel)) {
                    $csvpriceperoption = $option->getId();
                }
                if (in_array($option->getType(), $types)) {
                    foreach ($option->getValues() as $value) {
                        if ($value->getPriceType() == 'percent') {
                            $optionPercent[$value->getOptionId()][$value->getOptionTypeId()] = $value->getPrice();
                        }
                    }
                } else {
                    if ($option->getPriceType() == 'percent') {
                        $optionPercent[$option->getOptionId()] = $option->getPrice();
                    }
                }
            }
        }

        return [$optionPercent, $row, $col, $csvpriceperoption];
    }

    /**
     * [getPercent description]
     *
     * @param  [type] $optionPercent [description]
     * @param  [type] $optionID      [description]
     * @param  [type] $valueArr      [description]
     * @return [type]                [description]
     */
    public function getPercent($optionPercent, $optionID, $valueArr)
    {
        $managePercent = self::VALAUE_ZERO;
        if (is_array($optionPercent[$optionID])) {
            foreach ($optionPercent[$optionID] as $valueID => $values) {
                if (in_array($valueID, $valueArr)) {
                    $managePercent += $values;
                }
            }
        } elseif (is_numeric($optionPercent[$optionID])) {
            $managePercent += $optionPercent[$optionID];
        }
        return $managePercent;
    }

    /**
     * [getPercentOptionPrice description]
     *
     * @param  [type] $product       [description]
     * @param  [type] $optionPercent [description]
     * @param  [type] $finalPrice    [description]
     * @param  [type] $sheetPrice    [description]
     * @return [type]                [description]
     */
    public function getPercentOptionPrice($product, $optionPercent, $finalPrice, $sheetPrice)
    {
        $slipArr = ['option_ids', 'info_buyRequest'];
        $managePercent = self::VALAUE_ZERO;
        $basePrice = $product->getPrice();
        foreach ($product->getCustomOptions() as $options) {
            if (!in_array($options->getCode(), $slipArr)) {
                $valueArr = explode(',', $options->getValue());
                $optionID = preg_replace('/[^0-9]/', '', $options->getCode());
                if (isset($optionPercent[$optionID])) {
                    $managePercent += $this->getPercent($optionPercent, $optionID, $valueArr);
                }
            }
        }

        if ($managePercent > self::VALAUE_ZERO) {
            $finalPrice -= (($basePrice * $managePercent) / 100);
            $finalPrice += (($sheetPrice * $managePercent) / 100);
        }
        return $finalPrice;
    }

    /**
     * [getAddMarkupPrice description]
     *
     * @param  [type] $mproduct   [description]
     * @param  [type] $sheetPrice [description]
     * @return [type]             [description]
     */
    public function getAddMarkupPrice($mproduct, $sheetPrice)
    {
        $markupPrice = self::VALAUE_ZERO;
        $markuptype = $mproduct->getCsvPriceMarkupType();
        $markupvalue = $mproduct->getCsvPriceMarkupValue();
        if ($markuptype == 'percent' && $markupvalue != null) {
            $markupPrice = ($sheetPrice * $markupvalue / 100);
        }

        if ($markuptype === 'fixed' && $markupvalue != null) {
            $markupPrice = $markupvalue;
        }

        return ($sheetPrice + $markupPrice);
    }

    /**
     * [getPriceLevel description]
     *
     * @param  [type] $pricelevel     [description]
     * @param  [type] $pricesheetRows [description]
     * @param  [type] $rowvalue       [description]
     * @return [type]                 [description]
     */
    public function getPriceLevel($pricelevel, $pricesheetRows, $rowvalue)
    {
        if (empty($pricesheetRows)) {
            return $rowvalue;
        }

        $finalRowIndex = $rowvalue;
        $finalRowIndexflag = true;
        $count = self::VALAUE_ZERO;
        while ($finalRowIndexflag) {
            if (in_array($rowvalue, $pricesheetRows)) {
                break;
            } else {
                if ($pricelevel) {
                    $rowvalue--;
                } else {
                    $rowvalue++;
                }
            }

            if ($pricelevel) {
                if ($rowvalue == min($pricesheetRows)) {
                    $rowvalue=min($pricesheetRows);
                    $finalRowIndex = $rowvalue;
                    break;
                }
            } else {
                if ($rowvalue == max($pricesheetRows)) {
                    $rowvalue=max($pricesheetRows);
                    $finalRowIndex = $rowvalue;
                    break;
                }
            }
            if ($count == self::VALAUE_ONETHOUSAND) {
                $finalRowIndex = $rowvalue;
                break;
            }
            $count++;
            $finalRowIndex = $rowvalue;
        }

        return $finalRowIndex;
    }
}
