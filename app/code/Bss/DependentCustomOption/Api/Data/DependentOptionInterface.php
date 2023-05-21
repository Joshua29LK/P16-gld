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

interface DependentOptionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Const
     */
    const KEY_DEPENDENT_ID = 'dependent_id';
    const KEY_DCO_REQUIRE = 'bss_dco_require';
    const KEY_PRODUCT_SKU = 'product_sku';
    const KEY_OPTION_ID = 'option_id';
    const KEY_TITLE = 'title';
    const KEY_TYPE = 'type';
    const KEY_SORT_ORDER = 'sort_order';
    const KEY_IS_REQUIRE = 'is_require';
    const KEY_PRICE = 'price';
    const KEY_PRICE_TYPE = 'price_type';
    const KEY_SKU = 'sku';
    const KEY_FILE_EXTENSION = 'file_extension';
    const KEY_MAX_CHARACTERS = 'max_characters';
    const KEY_IMAGE_SIZE_Y = 'image_size_y';
    const KEY_IMAGE_SIZE_X = 'image_size_x';
    const KEY_VALUES = 'values';

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
     * Get product SKU
     *
     * @return string
     */
    public function getProductSku();

    /**
     * Set product SKU
     *
     * @param string $sku
     * @return $this
     */
    public function setProductSku(string $sku);

    /**
     * Get option id
     *
     * @return int|null
     */
    public function getOptionId();

    /**
     * Set option id
     *
     * @param int $optionId
     * @return $this
     */
    public function setOptionId(int $optionId);

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
     * Get option type
     *
     * @return string
     */
    public function getType();

    /**
     * Set option type
     *
     * @param string $type
     * @return $this
     */
    public function setType(string $type);

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
     * Get is require
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsRequire();

    /**
     * Set is require
     *
     * @param bool $isRequired
     * @return $this
     */
    public function setIsRequire(bool $isRequired);

    /**
     * Get price
     *
     * @return float|null
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
     * @return string|null
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
     * @return string|null
     */
    public function getFileExtension();

    /**
     * @param string $fileExtension
     * @return $this
     */
    public function setFileExtension(string $fileExtension);

    /**
     * @return int|null
     */
    public function getMaxCharacters();

    /**
     * @param int $maxCharacters
     * @return $this
     */
    public function setMaxCharacters(int $maxCharacters);

    /**
     * @return int|null
     */
    public function getImageSizeX();

    /**
     * @param int $imageSizeX
     * @return $this
     */
    public function setImageSizeX(int $imageSizeX);

    /**
     * @return int|null
     */
    public function getImageSizeY();

    /**
     * @param int $imageSizeY
     * @return $this
     */
    public function setImageSizeY(int $imageSizeY);

    /**
     * @return \Bss\DependentCustomOption\Api\Data\DependentOptionValuesInterface[]|null
     */
    public function getValues();

    /**
     * @param \Bss\DependentCustomOption\Api\Data\DependentOptionValuesInterface[] $values
     * @return $this
     */
    public function setValues(array $values = null);

    /**
     * @return int|bool
     */
    public function getDcoRequire();

    /**
     * @param string|int|bool $dcoValue
     * @return $this
     */
    public function setDcoRequired(string $dcoValue);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Bss\DependentCustomOption\Api\Data\DependentOptionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Bss\DependentCustomOption\Api\Data\DependentOptionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Bss\DependentCustomOption\Api\Data\DependentOptionExtensionInterface $extensionAttributes
    );
}
