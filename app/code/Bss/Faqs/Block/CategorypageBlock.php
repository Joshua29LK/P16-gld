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

class CategorypageBlock extends \Bss\Faqs\Block\ModuleBlock
{
    /**
     * Prepare layout
     *
     * @return CategorypageBlock|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _prepareLayout()
    {
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb(
                'home',
                [
                    'label'=>__('Home'),
                    'title'=>__('Go to Home Page'),
                    'link'=>$this->getRequest()->getBaseUrl()
                ]
            );
            $breadcrumbs->addCrumb(
                'faq_main',
                [
                    'label'=>__('FAQs Main Page'),
                    'title'=>__('Go to FAQs Main Page'),
                    'link'=> $this->getUrl('bss_faqs/index/index')
                ]
            );
            $breadcrumbs->addCrumb(
                'faq_category',
                [
                    'label' => __('Category')
                ]
            );
        }
    }

    /**
     * Get Category ID
     *
     * @return int
     */
    public function getCategoryId()
    {
        return $this->getRegistryData('category_id');
    }

    /**
     * Get URL Type
     *
     * @return String
     */
    public function getParamType()
    {
        return $this->getRegistryData('param_type');
    }

    /**
     * Get URL Param value
     *
     * @return String
     */
    public function getParamValue()
    {
        return $this->getRegistryData('param_value');
    }
}
