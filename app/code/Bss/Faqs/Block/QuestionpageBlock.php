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

class QuestionpageBlock extends \Bss\Faqs\Block\ModuleBlock
{
    /**
     * Prepare layout
     *
     * @return QuestionpageBlock|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _prepareLayout()
    {
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->getRequest()->getBaseUrl()
                ]
            );
            $breadcrumbs->addCrumb(
                'faq_main',
                [
                    'label' => __('FAQs Main Page'),
                    'title' => __('Go to FAQs Main Page'),
                    'link' => $this->getUrl('bss_faqs/index/index')
                ]
            );
            if ($this->getCategory('category_id') !== null) {
                $breadcrumbs->addCrumb(
                    'faq_category',
                    [
                        'label' => __('Category'),
                        'title' => __('Category'),
                        'link' => $this->getUrl('bss_faqs/category/view', ['id' => $this->getCategory()])
                    ]
                );
            }
            $breadcrumbs->addCrumb(
                'faq_question_detail',
                [
                    'label'=>__('FAQ Detail')
                ]
            );
        }
    }

    /**
     * Get URL type
     *
     * @return String
     */
    public function getParamType()
    {
        return $this->getRegistryData('param_type');
    }

    /**
     * Get Param Value
     *
     * @return String
     */
    public function getParamValue()
    {
        return $this->getRegistryData('param_value');
    }

    /**
     * Get Category ID
     *
     * @return int|null
     */
    public function getCategory()
    {
        return $this->getRegistryData('category_id');
    }
}
