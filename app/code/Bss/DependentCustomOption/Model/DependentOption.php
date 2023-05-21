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

use Bss\DependentCustomOption\Api\Data\DependentOptionInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class DependentOption extends AbstractExtensibleModel implements DependentOptionInterface
{
    /**
     * @inheritDoc
     */
    public function getDependentId()
    {
        return $this->getData(self::KEY_DEPENDENT_ID);
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setDependentId($id)
    {
        return $this->setData(self::KEY_DEPENDENT_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getProductSku()
    {
        return $this->getData(self::KEY_PRODUCT_SKU);
    }

    /**
     * @inheritDoc
     */
    public function setProductSku(string $sku)
    {
        return $this->setData(self::KEY_PRODUCT_SKU, $sku);
    }

    /**
     * @inheritDoc
     */
    public function getOptionId()
    {
        return $this->getData(self::KEY_OPTION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOptionId(int $optionId)
    {
        return $this->setData(self::KEY_OPTION_ID, $optionId);
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->getData(self::KEY_TITLE);
    }

    /**
     * @inheritDoc
     */
    public function setTitle(string $title)
    {
        return $this->setData(self::KEY_TITLE, $title);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->getData(self::KEY_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType(string $type)
    {
        return $this->setData(self::KEY_TYPE, $type);
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return $this->getData(self::KEY_SORT_ORDER);
    }

    /**
     * @inheritDoc
     */
    public function setSortOrder(int $sortOrder)
    {
        return $this->setData(self::KEY_SORT_ORDER, $sortOrder);
    }

    /**
     * @inheritDoc
     */
    public function getIsRequire()
    {
        return $this->getData(self::KEY_IS_REQUIRE);
    }

    /**
     * @inheritDoc
     */
    public function setIsRequire(bool $isRequired)
    {
        return $this->setData(self::KEY_IS_REQUIRE, $isRequired);
    }

    /**
     * @inheritDoc
     */
    public function getPrice()
    {
        return $this->getData(self::KEY_PRICE);
    }

    /**
     * @inheritDoc
     */
    public function setPrice(float $price)
    {
        return $this->setData(self::KEY_PRICE, $price);
    }

    /**
     * @inheritDoc
     */
    public function getPriceType()
    {
        return $this->getData(self::KEY_PRICE_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setPriceType(string $priceType)
    {
        return $this->setData(self::KEY_PRICE_TYPE, $priceType);
    }

    /**
     * @inheritDoc
     */
    public function getSku()
    {
        return $this->getData(self::KEY_SKU);
    }

    /**
     * @inheritDoc
     */
    public function setSku(string $sku)
    {
        return $this->setData(self::KEY_SKU, $sku);
    }

    /**
     * @inheritDoc
     */
    public function getFileExtension()
    {
        return $this->getData(self::KEY_FILE_EXTENSION);
    }

    /**
     * @inheritDoc
     */
    public function setFileExtension(string $fileExtension)
    {
        return $this->setData(self::KEY_FILE_EXTENSION, $fileExtension);
    }

    /**
     * @inheritDoc
     */
    public function getMaxCharacters()
    {
        return $this->getData(self::KEY_MAX_CHARACTERS);
    }

    /**
     * @inheritDoc
     */
    public function setMaxCharacters(int $maxCharacters)
    {
        return $this->setData(self::KEY_MAX_CHARACTERS, $maxCharacters);
    }

    /**
     * @inheritDoc
     */
    public function getImageSizeX()
    {
        return $this->getData(self::KEY_IMAGE_SIZE_X);
    }

    /**
     * @inheritDoc
     */
    public function setImageSizeX(int $imageSizeX)
    {
        return $this->setData(self::KEY_IMAGE_SIZE_X, $imageSizeX);
    }

    /**
     * @inheritDoc
     */
    public function getImageSizeY()
    {
        return $this->getData(self::KEY_IMAGE_SIZE_Y);
    }

    /**
     * @inheritDoc
     */
    public function setImageSizeY(int $imageSizeY)
    {
        return $this->setData(self::KEY_IMAGE_SIZE_Y, $imageSizeY);
    }

    /**
     * @inheritDoc
     */
    public function getValues()
    {
        return $this->getData(self::KEY_VALUES);
    }

    /**
     * @inheritDoc
     */
    public function setValues(array $values = null)
    {
        return $this->setData(self::KEY_VALUES, $values);
    }

    /**
     * @inheritDoc
     */
    public function getDcoRequire()
    {
        return $this->getData(self::KEY_DCO_REQUIRE);
    }

    /**
     * @inheritDoc
     */
    public function setDcoRequired(string $dcoValue)
    {
        return $this->setData(self::KEY_DCO_REQUIRE, $dcoValue);
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
        \Bss\DependentCustomOption\Api\Data\DependentOptionExtensionInterface $extensionAttributes
    ) {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
