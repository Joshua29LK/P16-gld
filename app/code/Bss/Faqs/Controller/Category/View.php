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
namespace Bss\Faqs\Controller\Category;

class View extends \Bss\Faqs\Controller\AbstractFaq
{
    /**
     * @var string
     */
    protected $faqTitle = 'FAQs Category';

    /**
     * Init faqView
     *
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function initFaqView()
    {
        $url = $this->getRequest()->getParam('url');
        $id = $this->getRequest()->getParam('id');
        if ($url != null) {
            $this->coreRegistry->register('param_type', 'url');
            $this->coreRegistry->register('param_value', $url);
            $category = $this->faqRepository->getCategoryByUrl($url, 'time');
        } else {
            $this->coreRegistry->register('param_type', 'id');
            $this->coreRegistry->register('param_value', $id);
            $category = $this->faqRepository->getCategoryById($id, 'time');
        }
        $this->coreRegistry->register('category_id', $category['faq_category_id']);
    }
}
