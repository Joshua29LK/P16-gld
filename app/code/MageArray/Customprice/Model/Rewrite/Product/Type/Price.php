<?php
/**
 * Copyright ? 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageArray\Customprice\Model\Rewrite\Product\Type;

use Magento\Catalog\Model\Product;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Price extends \Magento\Catalog\Model\Product\Type\Price
{
    /**
     * [$_dataHelper description]
     *
     * @var [type]
     */
    protected $_dataHelper;

    /**
     * [$_objectManager description]
     *
     * @var [type]
     */
    protected $_objectManager;

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
     * @param \Magento\Framework\ObjectManagerInterface                  $objectManager    [description]
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        PriceCurrencyInterface $priceCurrency,
        GroupManagementInterface $groupManagement,
        \Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory $tierPriceFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \MageArray\Customprice\Helper\Data $dataHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_eventManager = $eventManager;
        $this->_localeDate = $localeDate;
        $this->priceCurrency = $priceCurrency;
        $this->_groupManagement = $groupManagement;
        $this->tierPriceFactory = $tierPriceFactory;
        $this->config = $config;
        $this->_dataHelper = $dataHelper;
        $this->_objectManager = $objectManager;
    }

    /**
     * [getFinalPrice description]
     *
     * @param  [type] $qty     [description]
     * @param  [type] $product [description]
     * @return [type]          [description]
     */
    public function getFinalPrice($qty, $product)
    {
        if ($qty === null && $product->getCalculatedFinalPrice() !== null) {
            return $product->getCalculatedFinalPrice();
        }

        $finalPrice = $this->getBasePrice($product, $qty);
        $product->setFinalPrice($finalPrice);

        $this->_eventManager
            ->dispatch(
                'catalog_product_get_final_price',
                ['product' => $product, 'qty' => $qty]
            );

        $finalPrice = $product->getData('final_price');
        // Added code to calculate custom option price - Start
        $finalPrice = $this->_applyCustomPrice($product, $finalPrice);
        // Added code to calculate custom option price - End
        $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);
        $finalPrice = max(0, $finalPrice);
        $product->setFinalPrice($finalPrice);

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
        $active = $this->_dataHelper
            ->getStoreConfig('customprice/general/active');
        if ($active == 1) {
            if ($optionIds = $product->getCustomOption('option_ids')) {
                $mproduct = $this->_objectManager
                    ->get(\Magento\Catalog\Model\Product::class)
                    ->load($product->getId());

                $basePrice = $finalPrice;

                $prdRowLabel = ($mproduct->getRowLabels() != '') ? strtolower($mproduct->getRowLabels()):'';
                $prdColumnLabel = ($mproduct->getColumnLabels() != '') ? strtolower($mproduct->getColumnLabels()) : '';

                if ($prdRowLabel != "") {
                    $xlabel = $prdRowLabel;
                } else {
                    $xlabel = strtolower($this->_dataHelper
                        ->getStoreConfig('customprice/general/row_label'));
                }

                $row = false;

                if ($prdColumnLabel != "") {
                    $ylabel = $prdColumnLabel;
                } else {
                    $ylabel = strtolower($this->_dataHelper
                        ->getStoreConfig('customprice/general/column_label'));
                }

                $col = false;
                foreach (explode(',', $optionIds->getValue()) as $optionId) {
                    if ($option = $product->getOptionById($optionId)) {
                        $title = ($option->getTitle() != '') ? strtolower($option->getTitle()) : '';
                        if ($title == $xlabel) {
                            $row = $option->getId();
                        }
                        if ($title == $ylabel) {
                            $col = $option->getId();
                        }
                    }
                }

                $csvfile = $mproduct->getCsvPrice();
                if ($row && $col && !empty($csvfile)) {
                    $csvPrice = json_decode($csvfile, true);
                    $confItemOption = $product
                        ->getCustomOption('option_' . $row);
                    $rowvalue = $confItemOption->getValue();

                    $confItemOption = $product
                        ->getCustomOption('option_' . $col);
                    $columnvalue = $confItemOption->getValue();

                    $pricesheet = $csvPrice['vals'];
                    $pricesheetRows = $pricesheet['row'];
                    $pricesheetCols = $pricesheet['col'];
                    // check price min label or not
                    $pricelevel = (int)$mproduct->getPricemin();
                    if ($pricelevel == 2) {
                        $pricelevel = (int)$this->_dataHelper
                            ->getStoreConfig('customprice/general/pricemin');
                    }

                    if (!$pricelevel) {
                        $finalRowIndexflag = false;
                        $finalColIndexflag = false;
                        while (!$finalRowIndexflag) {
                            if (in_array($rowvalue, $pricesheetRows)) {
                                $finalRowIndex = $rowvalue;
                                $finalRowIndexflag = true;
                                break;
                            } else {
                                $rowvalue++;
                            }
                        }

                        while (!$finalColIndexflag) {
                            if (in_array($columnvalue, $pricesheetCols)) {
                                $finalColumnIndex = $columnvalue;
                                $finalColIndexflag = true;
                                break;
                            } else {
                                $columnvalue++;
                            }
                        }
                        $sheetPrice = $csvPrice['pricesheet'][$finalRowIndex][$finalColumnIndex];
                        $inclbaseprice = $mproduct->getIncludeBaseprice();
                        if ($inclbaseprice == 2) {
                            $inclbaseprice = $this->_dataHelper
                                ->getStoreConfig(
                                    'customprice/general/include_baseprice'
                                );
                        }

                        if ($inclbaseprice) {
                            $finalPrice = $basePrice + $sheetPrice;
                        } else {
                            $finalPrice = $sheetPrice;
                        }
                    } else {
                        $finalRowIndexflag = false;
                        $finalColIndexflag = false;
                        while (!$finalRowIndexflag) {
                            if (in_array($rowvalue, $pricesheetRows)) {
                                $finalRowIndex = $rowvalue;
                                $finalRowIndexflag = true;
                                break;
                            } else {
                                $rowvalue--;
                            }
                        }
                        while (!$finalColIndexflag) {
                            if (in_array($columnvalue, $pricesheetCols)) {
                                $finalColumnIndex = $columnvalue;
                                $finalColIndexflag = true;
                                break;
                            } else {
                                $columnvalue--;
                            }
                        }
                        $sheetPrice = $csvPrice['pricesheet'][$finalRowIndex][$finalColumnIndex];
                        $inclbaseprice = $mproduct->getIncludeBaseprice();
                        if ($inclbaseprice == 2) {
                            $inclbaseprice = $this->_dataHelper
                                ->getStoreConfig(
                                    'customprice/general/include_baseprice'
                                );
                        }

                        if ($inclbaseprice) {
                            $finalPrice = $basePrice + $sheetPrice;
                        } else {
                            $finalPrice = $sheetPrice;
                        }
                    }
                }
                $productObj = $this->_objectManager
                    ->get(\Magento\Catalog\Model\Product::class)
                    ->load($product->getId());
                $markuptype = $productObj->getCsvPriceMarkupType();
                $markupvalue = $productObj->getCsvPriceMarkupValue();
                if ($markuptype == 'percent' && $markupvalue != null) {
                    $finalPrice = $finalPrice +
                        ($finalPrice * $markupvalue / 100);
                }
                if ($markuptype === 'fixed' && $markupvalue != null) {
                    $finalPrice = $finalPrice + $markupvalue;
                }
            }
            return $finalPrice;
        } else {
            return $finalPrice;
        }
    }
}
