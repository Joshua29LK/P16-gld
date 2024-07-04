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
namespace Bss\Faqs\Block\Widget\Grid\Column;

class FilterStore extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Store
{
    /**
     * Get condition
     *
     * @return array|null
     */
    public function getCondition()
    {
        $value = $this->getValue();
        if ($value === null || $value == self::ALL_STORE_VIEWS) {
            return null;
        }
        if ($value == '_deleted_') {
            return ['null' => true];
        } else {
            return ['finset' => $value];
        }
    }
}
