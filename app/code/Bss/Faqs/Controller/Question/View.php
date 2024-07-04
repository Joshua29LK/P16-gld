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
namespace Bss\Faqs\Controller\Question;

use Magento\Framework\Exception\NoSuchEntityException;

class View extends \Bss\Faqs\Controller\AbstractFaq
{
    /**
     * @var string
     */
    protected $faqTitle = 'FAQs Question';

    /**
     * Init faq view
     *
     * @return void
     * @throws NoSuchEntityException
     */
    protected function initFaqView()
    {
        $requestData = $this->getRequest();
        $url = $requestData->getParam('url');
        $id = $requestData->getParam('id');
        $categoryId = $requestData->getParam('category', null);
        if ($url != null) {
            $this->coreRegistry->register('param_type', 'url');
            $this->coreRegistry->register('param_value', $url);
            $faq = $this->faqRepository->getFaqByUrl($url);
        } else {
            $this->coreRegistry->register('param_type', 'id');
            $this->coreRegistry->register('param_value', $id);
            $faq = $this->faqRepository->getFaqById($id);
        }
        $this->coreRegistry->register('category_id', $categoryId);
        $this->coreRegistry->register('faq_id', $faq['faq_id']);
    }
}
