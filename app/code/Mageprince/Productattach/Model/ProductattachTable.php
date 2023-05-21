<?php

/**
 * MagePrince
 * Copyright (C) 2020 Mageprince <info@mageprince.com>
 *
 * @package Mageprince_Productattach
 * @copyright Copyright (c) 2020 Mageprince (http://www.mageprince.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MagePrince <info@mageprince.com>
 */

namespace Mageprince\Productattach\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Mageprince\Productattach\Api\Data\ProductattachTableInterface;
use Mageprince\Productattach\Model\ResourceModel\Productattach as ProductattachResourceModel;

class ProductattachTable extends AbstractModel implements ProductattachTableInterface, IdentityInterface
{
    const CACHE_TAG = 'prince_productattach_productattachtable';
    protected $_cacheTag = 'prince_productattach_productattachtable';
    protected $_eventPrefix = 'prince_productattach_productattachtable';

    protected function _construct()
    {
        $this->_init(ProductattachResourceModel::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return int
     */
    public function getAttachmentId()
    {
        return $this->getData("productattach_id");
    }

    /**
     * @param int $val
     * @return int
     */
    public function setAttachmentId($val)
    {
        $this->setData("productattach_id", $val);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData("name");
    }

    /**
     * @param  string $val
     * @return void
     */
    public function setName($val)
    {
        $this->setData("name", $val);
    }
    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getData("description");
    }

    /**
     * @param  string $val
     * @return void
     */
    public function setDescription($val)
    {
        $this->setData("description", $val);
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->getData("file");
    }

    /**
     * @param  string $val
     * @return void
     */
    public function setFile($val)
    {
        $this->setData("file", $val);
    }

    /**
     * @return string
     */
    public function getFileType()
    {
        return $this->getData("file_type");
    }

    /**
     * @param  string $val
     * @return void
     */
    public function setFileType($val)
    {
        $this->setData("file_type", $val);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->getData("url");
    }

    /**
     * @param  string $val
     * @return void
     */
    public function setUrl($val)
    {
        $this->setData("url", $val);
    }

    /**
     * @return string
     */
    public function getStore()
    {
        return $this->getData("store");
    }

    /**
     * @param  string $val
     * @return void
     */
    public function setStore($val)
    {
        $this->setData("store", $val);
    }

    /**
     * @return string
     */
    public function getCustomerGroup()
    {
        return $this->getData("customer_group");
    }

    /**
     * @param  string $val
     * @return void
     */
    public function setCustomerGroup($val)
    {
        $this->setData("customer_group", $val);
    }

    /**
     * @return string
     */
    public function getProducts()
    {
        return $this->getData("products");
    }

    /**
     * @param  string $val
     * @return void
     */
    public function setProducts($val)
    {
        $this->setData("products", $val);
    }

    /**
     * @return int
     */
    public function getActive()
    {
        return $this->getData("active");
    }

    /**
     * @param  string $val
     * @return void
     */
    public function setActive($val)
    {
        $this->setData("active", $val);
    }
}
