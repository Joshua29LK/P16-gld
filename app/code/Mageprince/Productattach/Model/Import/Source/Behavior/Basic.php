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

namespace Mageprince\Productattach\Model\Import\Source\Behavior;

use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Source\Import\AbstractBehavior;

class Basic extends AbstractBehavior
{
    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            Import::BEHAVIOR_APPEND => __('Add/Update'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return 'importmpfilebasic';
    }
}
