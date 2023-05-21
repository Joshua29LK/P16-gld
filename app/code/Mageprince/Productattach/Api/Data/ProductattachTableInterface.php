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

namespace Mageprince\Productattach\Api\Data;

interface ProductattachTableInterface
{
    /**
     * @return int
     */
    public function getAttachmentId();

    /**
     * @param int $val
     * @return int
     */
    public function setAttachmentId($val);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param  string $val
     * @return string
     */
    public function setName($val);

    /**
     * @return string mixed
     */
    public function getDescription();

    /**
     * @param string $val
     * @return string
     */
    public function setDescription($val);

    /**
     * @return string
     */
    public function getFile();

    /**
     * @param string $val
     * @return string
     */
    public function setFile($val);

    /**
     * @return string
     */
    public function getFileType();

    /**
     * @param string $val
     * @return string
     */
    public function setFileType($val);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $val
     * @return string
     */
    public function setUrl($val);

    /**
     * @return string
     */
    public function getStore();

    /**
     * @param string $val
     * @return string
     */
    public function setStore($val);

    /**
     * @return string
     */
    public function getCustomerGroup();

    /**
     * @param string $val
     * @return string
     */
    public function setCustomerGroup($val);

    /**
     * @return string[]
     */
    public function getProducts();

    /**
     * @param string[] $val
     * @return string[]
     */
    public function setProducts($val);

    /**
     * @return int
     */
    public function getActive();

    /**
     * @param string $val
     * @return int
     */
    public function setActive($val);
}
