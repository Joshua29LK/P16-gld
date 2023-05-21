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
 * @package    Bss_DependentCustomOption
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\DependentCustomOption\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class MultipleParent implements ArrayInterface
{
    const USE_GLOBAL_CONFIG = 'global';
    const WHEN_AT_LEAST_ONE = 'atleast_one';
    const WHEN_ALL_PARENT ='all';
    /**
     * ToOptionArray
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::WHEN_AT_LEAST_ONE, 'label' => __('When at least one parent value is selected')],
            ['value' => self::WHEN_ALL_PARENT, 'label' => __('When all parent values are selected')]
        ];
    }
}
