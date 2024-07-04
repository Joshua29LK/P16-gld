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
 * @package    Bss_Faqs
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\Faqs\Model\Config\Source;

class QuestionDisplay implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * To option arra
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'expand', 'label' => __('Expand')],
            ['value' => 'collapse', 'label' => __('Collapse')]
        ];
    }
}
