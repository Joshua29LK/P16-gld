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

interface FileiconInterface
{
    const ICON_IMAGE = 'icon_image';
    const FILEICON_ID = 'fileicon_id';
    const ICON_EXT = 'icon_ext';

    /**
     * Get fileicon_id
     * @return string|null
     */
    public function getFileiconId();

    /**
     * Set fileicon_id
     *
     * @param string $fileicon_id
     * @return FileiconInterface
     */
    public function setFileiconId($fileiconId);

    /**
     * Get icon_ext
     * @return string|null
     */
    public function getIconExt();

    /**
     * Set icon_ext
     *
     * @param string $icon_ext
     * @return FileiconInterface
     */
    public function setIconExt($icon_ext);

    /**
     * Get icon_image
     * @return string|null
     */
    public function getIconImage();

    /**
     * Set icon_image
     *
     * @param string $icon_image
     * @return FileiconInterface
     */
    public function setIconImage($icon_image);
}
