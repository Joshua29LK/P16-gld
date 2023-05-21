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
namespace Bss\DependentCustomOption\Model;

use Bss\DependentCustomOption\Api\Data\ProductInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class Product extends AbstractExtensibleModel implements ProductInterface
{
    /**
     * @inheritDoc
     */
    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    /**
     * @inheritDoc
     */
    public function setSku(string $sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getAttributeSetId()
    {
        return $this->getData(self::ATTRIBUTE_SET_ID);
    }

    /**
     * @inheritDoc
     */
    public function setAttributeSetId(int $attributeSetId)
    {
        return $this->setData(self::ATTRIBUTE_SET_ID, $attributeSetId);
    }

    /**
     * @inheritDoc
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    /**
     * @inheritDoc
     */
    public function setPrice(float  $price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus(int $status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getVisibility()
    {
        return $this->getData(self::VISIBILITY);
    }

    /**
     * @inheritDoc
     */
    public function setVisibility(int $visibility)
    {
        return $this->setData(self::VISIBILITY, $visibility);
    }

    /**
     * @inheritDoc
     */
    public function getTypeId()
    {
        return $this->getData(self::TYPE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setTypeId(string $typeId)
    {
        return $this->setData(self::TYPE_ID, $typeId);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(string $createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt(string $updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritDoc
     */
    public function getWeight()
    {
        return $this->getData(self::WEIGHT);
    }

    /**
     * @inheritDoc
     */
    public function setWeight(float $weight)
    {
        return $this->setData(self::WEIGHT, $weight);
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(
        \Bss\DependentCustomOption\Api\Data\ProductExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * @inheritDoc
     */
    public function getProductLinks()
    {
        return $this->getData(self::PRODUCT_LINKS);
    }

    /**
     * @inheritDoc
     */
    public function setProductLinks(array $links = null)
    {
        return $this->setData(self::PRODUCT_LINKS, $links);
    }

    /**
     * @inheritDoc
     */
    public function getOptions()
    {
        return $this->getData(self::OPTIONS);
    }

    /**
     * @inheritDoc
     */
    public function setOptions(array $options = null)
    {
        return $this->setData(self::OPTIONS, $options);
    }

    /**
     * @inheritDoc
     */
    public function getMediaGalleryEntries()
    {
        return $this->getData(self::MEDIA_GALLERY_ENTRIES);
    }

    /**
     * @inheritDoc
     */
    public function setMediaGalleryEntries(array $mediaGalleryEntries = null)
    {
        return $this->setData(self::MEDIA_GALLERY_ENTRIES, $mediaGalleryEntries);
    }

    /**
     * @inheritDoc
     */
    public function getTierPrices()
    {
        return $this->getData(self::TIER_PRICE);
    }

    /**
     * @inheritDoc
     */
    public function setTierPrices(array $tierPrices = null)
    {
        return $this->setData(self::TIER_PRICE, $tierPrices);
    }
}
