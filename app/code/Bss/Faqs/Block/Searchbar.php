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
namespace Bss\Faqs\Block;

class Searchbar extends \Bss\Faqs\Block\ModuleBlock
{
    /**
     * Get category id
     *
     * @return mixed|string
     */
    public function getCategoryId()
    {
        $category = $this->getRegistryData('category_id');
        return ($category !== null) ? $category : '0';
    }
}
