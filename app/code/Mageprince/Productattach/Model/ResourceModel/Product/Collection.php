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

namespace Mageprince\Productattach\Model\ResourceModel\Product;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mageprince\Productattach\Model\Product as ProductModel;
use Mageprince\Productattach\Model\ResourceModel\Product as ProductResourceModel;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(
            ProductModel::class,
            ProductResourceModel::class
        );
    }
}
