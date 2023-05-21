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

use Bss\DependentCustomOption\Api\Data\DependentOptionValuesInterface;
use Magento\Framework\DataObject;

class DependentOptionValues extends DataObject implements DependentOptionValuesInterface
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
    public function getDependValue()
    {
        return $this->getData(self::KEY_DEPEND_VALUE);
    }

    /**
     * @inheritDoc
     */
    public function setDependValue(string $dependValue)
    {
        return $this->setData(self::KEY_DEPEND_VALUE, $dependValue);
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
    public function getOptionTypeId()
    {
        return $this->getData(self::KEY_OPTION_TYPE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOptionTypeId(int $optionTypeId)
    {
        return $this->setData(self::KEY_OPTION_TYPE_ID, $optionTypeId);
    }
}
