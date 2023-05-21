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
namespace Bss\DependentCustomOption\Api\Data;

interface DependentOptionValuesInterface
{
    /**
     * Const
     */
    const KEY_DEPENDENT_ID = 'dependent_id';
    const KEY_DEPEND_VALUE = 'depend_value';
    const KEY_PRODUCT_ID = 'product_id';
    const KEY_OPTION_ID = 'option_id';
    const TYPE_PERCENT = 'percent';
    const KEY_TITLE = 'title';
    const KEY_SORT_ORDER = 'sort_order';
    const KEY_PRICE = 'price';
    const KEY_PRICE_TYPE = 'price_type';
    const KEY_SKU = 'sku';
    const KEY_OPTION_TYPE_ID = 'option_type_id';

    /**
     * @return int
     */
    public function getDependentId();

    /**
     * @param int $id
     * @return $this
     */
    public function setDependentId($id);

    /**
     * @return string
     */
    public function getDependValue();

    /**
     * @param string $dependValue
     * @return $this
     */
    public function setDependValue(string $dependValue);

    /**
     * Get option title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set option title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title);

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * Set sort order
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder(int $sortOrder);

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice();

    /**
     * Set price
     *
     * @param float $price
     * @return $this
     */
    public function setPrice(float $price);

    /**
     * Get price type
     *
     * @return string
     */
    public function getPriceType();

    /**
     * Set price type
     *
     * @param string $priceType
     * @return $this
     */
    public function setPriceType(string $priceType);

    /**
     * Get Sku
     *
     * @return string|null
     */
    public function getSku();

    /**
     * Set Sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku(string $sku);

    /**
     * Get Option type id
     *
     * @return int|null
     */
    public function getOptionTypeId();

    /**
     * Set Option type id
     *
     * @param int $optionTypeId
     * @return $this
     */
    public function setOptionTypeId(int $optionTypeId);
}
