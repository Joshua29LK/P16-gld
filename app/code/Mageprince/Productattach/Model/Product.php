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
use Mageprince\Productattach\Model\ResourceModel\Product as ProductResourceModel;

class Product extends AbstractModel
{
    public function _construct()
    {
        $this->_init(ProductResourceModel::class);
    }
}
