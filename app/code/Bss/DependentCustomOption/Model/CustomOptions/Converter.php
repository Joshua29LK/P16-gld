<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_DependentCustomOption
 * @author     Extension Team
 * @copyright  Copyright (c) 2020-2021 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\DependentCustomOption\Model\CustomOptions;

use Bss\DependentCustomOption\Api\Data\DependentOptionInterface;
use Bss\DependentCustomOption\Api\Data\DependentOptionValuesInterface;
use Bss\DependentCustomOption\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface;
use Magento\Framework\DataObject;

class Converter
{
    /**
     * @param DependentOptionInterface|ProductCustomOptionInterface $option
     * @param DependentOptionInterface|ProductCustomOptionInterface $targetSubject
     * @return DependentOptionInterface|ProductCustomOptionInterface
     */
    public function convertOption($option, $targetSubject)
    {
        if ($option->getOptionId()) {
            $targetSubject->setOptionId($option->getOptionId());
        }
        $targetSubject->setProductSku($option->getProductSku());
        $targetSubject->setPrice($option->getPrice() ?? 0);
        $targetSubject->setSku($option->getSku() ?? '');
        $targetSubject->setFileExtension($option->getFileExtension() ?? '');
        if ($option->getImageSizeX()) {
            $targetSubject->setImageSizeX($option->getImageSizeX());
        }
        if ($option->getImageSizeY()) {
            $targetSubject->setImageSizeY($option->getImageSizeY());
        }
        $targetSubject->setIsRequire($option->getIsRequire());
        if ($option->getMaxCharacters()) {
            $targetSubject->setMaxCharacters($option->getMaxCharacters());
        }
        if ($option->getPriceType()) {
            $targetSubject->setPriceType($option->getPriceType());
        }
        $targetSubject->setSortOrder($option->getSortOrder());
        $targetSubject->setType($option->getType());
        $targetSubject->setTitle($option->getTitle());
        $targetSubject->setDcoRequired($option->getIsRequire());
        return $targetSubject;
    }

    /**
     * @param DependentOptionValuesInterface|ProductCustomOptionValuesInterface|DataObject $value
     * @param DependentOptionValuesInterface|ProductCustomOptionValuesInterface $targetValue
     * @return DependentOptionValuesInterface|ProductCustomOptionValuesInterface
     */
    public function convertOptionValues($value, $targetValue)
    {
        $targetValue->setSortOrder($value->getSortOrder());
        $targetValue->setPriceType(strtolower($value->getPriceType()));
        $targetValue->setSku($value->getSku() ?? '');
        if ($value->getOptionTypeId()) {
            $targetValue->setOptionTypeId($value->getOptionTypeId());
        }
        $targetValue->setTitle($value->getTitle());
        $targetValue->setPrice($value->getPrice());
        return $targetValue;
    }

    /**
     * @param ProductInterface|\Magento\Catalog\Api\Data\ProductInterface|DataObject $product
     * @param ProductInterface|\Magento\Catalog\Api\Data\ProductInterface|DataObject $targetProduct
     * @return ProductInterface|\Magento\Catalog\Api\Data\ProductInterface
     */
    public function convertProduct($product, $targetProduct)
    {
        $params = ProductInterface::CONVERTER_PARAMS;
        foreach ($params as $param) {
            $funcSet = $this->getKeyCamelcase($param, 'set');
            $funcGet = $this->getKeyCamelcase($param, 'get');
            if ($funcGet && $funcSet &&
                method_exists($targetProduct, $funcSet) &&
                method_exists($product, $funcGet)) {
                $targetProduct->$funcSet($product->$funcGet());
            }
        }
        return $targetProduct;
    }

    /**
     * @param string $key
     * @param string $action
     * @return bool|string
     */
    protected function getKeyCamelcase($key, $action)
    {
        $key = str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($key))));
        if (isset($key[0])) {
            return $action . $key;
        }
        return false;
    }
}
