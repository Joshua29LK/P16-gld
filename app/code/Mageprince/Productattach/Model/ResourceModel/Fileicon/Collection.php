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

namespace Mageprince\Productattach\Model\ResourceModel\Fileicon;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mageprince\Productattach\Model\Fileicon as FileIconModel;
use Mageprince\Productattach\Model\ResourceModel\Fileicon as FileIconResourceModel;

class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            FileIconModel::class,
            FileIconResourceModel::class
        );
    }
}
