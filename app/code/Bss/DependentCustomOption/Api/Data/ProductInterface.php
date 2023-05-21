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

interface ProductInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const ENTITY_ID = 'entity_id';
    const SKU = 'sku';
    const NAME = 'name';
    const PRICE = 'price';
    const WEIGHT = 'weight';
    const STATUS = 'status';
    const VISIBILITY = 'visibility';
    const ATTRIBUTE_SET_ID = 'attribute_set_id';
    const TYPE_ID = 'type_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const PRODUCT_LINKS = 'product_links';
    const OPTIONS = 'options';
    const MEDIA_GALLERY_ENTRIES = 'media_gallery_entries';
    const TIER_PRICE = 'tier_price';

    const CONVERTER_PARAMS = [
        self::ENTITY_ID,
        self::SKU,
        self::NAME,
        self::PRICE,
        self::WEIGHT,
        self::STATUS,
        self::VISIBILITY,
        self::ATTRIBUTE_SET_ID,
        self::TYPE_ID,
        self::CREATED_AT,
        self::UPDATED_AT,
        self::PRODUCT_LINKS,
        self::MEDIA_GALLERY_ENTRIES,
        self::TIER_PRICE,
    ];
    /**#@-*/

    /**
     * Product sku
     *
     * @return string
     */
    public function getSku();

    /**
     * Set product sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku(string $sku);

    /**
     * Product name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set product name
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name);

    /**
     * Product attribute set id
     *
     * @return int|null
     */
    public function getAttributeSetId();

    /**
     * Set product attribute set id
     *
     * @param int $attributeSetId
     * @return $this
     */
    public function setAttributeSetId(int $attributeSetId);

    /**
     * Product price
     *
     * @return float|null
     */
    public function getPrice();

    /**
     * Set product price
     *
     * @param float $price
     * @return $this
     */
    public function setPrice(float $price);

    /**
     * Product status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Set product status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status);

    /**
     * Product visibility
     *
     * @return int|null
     */
    public function getVisibility();

    /**
     * Set product visibility
     *
     * @param int $visibility
     * @return $this
     */
    public function setVisibility(int $visibility);

    /**
     * Product type id
     *
     * @return string|null
     */
    public function getTypeId();

    /**
     * Set product type id
     *
     * @param string $typeId
     * @return $this
     */
    public function setTypeId(string $typeId);

    /**
     * Product created date
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set product created date
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt);

    /**
     * Product updated date
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set product updated date
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt);

    /**
     * Product weight
     *
     * @return float|null
     */
    public function getWeight();

    /**
     * Set product weight
     *
     * @param float $weight
     * @return $this
     */
    public function setWeight(float $weight);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Bss\DependentCustomOption\Api\Data\ProductExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Bss\DependentCustomOption\Api\Data\ProductExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Bss\DependentCustomOption\Api\Data\ProductExtensionInterface $extensionAttributes
    );

    /**
     * Get product links info
     *
     * @return \Magento\Catalog\Api\Data\ProductLinkInterface[]|null
     */
    public function getProductLinks();

    /**
     * Set product links info
     *
     * @param \Magento\Catalog\Api\Data\ProductLinkInterface[] $links
     * @return $this
     */
    public function setProductLinks(array $links = null);

    /**
     * Get list of product options
     *
     * @return \Bss\DependentCustomOption\Api\Data\DependentOptionInterface[]|null
     */
    public function getOptions();

    /**
     * Set list of product options
     *
     * @param \Bss\DependentCustomOption\Api\Data\DependentOptionInterface[] $options
     * @return $this
     */
    public function setOptions(array $options = null);

    /**
     * Get media gallery entries
     *
     * @return \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface[]|null
     */
    public function getMediaGalleryEntries();

    /**
     * Set media gallery entries
     *
     * @param \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface[] $mediaGalleryEntries
     * @return $this
     */
    public function setMediaGalleryEntries(array $mediaGalleryEntries = null);

    /**
     * Gets list of product tier prices
     *
     * @return \Magento\Catalog\Api\Data\ProductTierPriceInterface[]|null
     */
    public function getTierPrices();

    /**
     * Sets list of product tier prices
     *
     * @param \Magento\Catalog\Api\Data\ProductTierPriceInterface[] $tierPrices
     * @return $this
     */
    public function setTierPrices(array $tierPrices = null);
}
