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
 * @package    Bss_CustomOptionImage
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\CustomOptionImage\Model\Config\Source;

class View implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * To option array
     *
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Show image when selected')],
            ['value' => 1, 'label' => __('Show all images')]
        ];
    }
}
