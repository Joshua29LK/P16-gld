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

namespace Mageprince\Productattach\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class TableViewColumns implements OptionSourceInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'header', 'label' => __('Table Header')],
            ['value' => 'icon', 'label' => __('File Icon')],
            ['value' => 'label', 'label' => __('File Label')],
            ['value' => 'description', 'label' => __('Description')],
            ['value' => 'size', 'label' => __('File Size')],
            ['value' => 'type', 'label' => __('File Type')],
            ['value' => 'download', 'label' => __('Download File')]
        ];
    }
}
