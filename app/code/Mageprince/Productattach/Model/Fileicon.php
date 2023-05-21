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

use Magento\Framework\Model\AbstractModel;
use Mageprince\Productattach\Api\Data\FileiconInterface;
use Mageprince\Productattach\Model\ResourceModel\Fileicon as FileiconResourceModel;

class Fileicon extends AbstractModel implements FileiconInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(FileiconResourceModel::class);
    }

    /**
     * Get fileicon_id
     *
     * @return string
     */
    public function getFileiconId()
    {
        return $this->getData(self::FILEICON_ID);
    }

    /**
     * Set fileicon_id
     *
     * @param string $fileiconId
     * @return \Mageprince\Productattach\Api\Data\FileiconInterface
     */
    public function setFileiconId($fileiconId)
    {
        return $this->setData(self::FILEICON_ID, $fileiconId);
    }

    /**
     * Get icon_ext
     *
     * @return string
     */
    public function getIconExt()
    {
        return $this->getData(self::ICON_EXT);
    }

    /**
     * Set icon_ext
     *
     * @param string $icon_ext
     * @return \Mageprince\Productattach\Api\Data\FileiconInterface
     */
    public function setIconExt($icon_ext)
    {
        return $this->setData(self::ICON_EXT, $icon_ext);
    }

    /**
     * Get icon_image
     *
     * @return string
     */
    public function getIconImage()
    {
        return $this->getData(self::ICON_IMAGE);
    }

    /**
     * Set icon_image
     *
     * @param string $icon_image
     * @return \Mageprince\Productattach\Api\Data\FileiconInterface
     */
    public function setIconImage($icon_image)
    {
        return $this->setData(self::ICON_IMAGE, $icon_image);
    }
}
