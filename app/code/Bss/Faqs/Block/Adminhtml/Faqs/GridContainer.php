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
namespace Bss\Faqs\Block\Adminhtml\Faqs;

class GridContainer extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Construct
     *
     * @return void
     */
    public function _construct()
    {
        $this->_headerText = __('FAQs Management');
        $this->_addButtonLabel = __('Add FAQ');
        parent::_construct();
    }

    /**
     * Get create url
     *
     * @return string
     */
    public function getCreateUrl()
    {
        $url = $this->getUrl('adminhtml/*/edit');

        return $url;
    }
}
