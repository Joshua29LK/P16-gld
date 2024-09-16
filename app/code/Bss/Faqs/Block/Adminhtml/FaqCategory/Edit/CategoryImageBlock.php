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
namespace Bss\Faqs\Block\Adminhtml\FaqCategory\Edit;

class CategoryImageBlock extends \Magento\Framework\View\Element\Template
{
    /**
     * Get image url
     *
     * @return string
     */
    public function getImageUrl()
    {
        return ($this->getData('category_image')) ?
        $this->getData('category_image') :
        $this->getViewFileUrl('Bss_Faqs::image/noimage.png');
    }
}
