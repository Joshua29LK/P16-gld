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
namespace Bss\Faqs\Controller\Json;

class ProductFaqSubmit extends AbstractAjax
{
    /**
     * Get json data
     *
     * @return array|\Magento\Framework\Phrase
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getJsonData()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Formkey invalidated'));
        }
        $question = $this->getRequest()->getParam('question');
        $productId = $this->getRequest()->getParam('product_id');
        $customer = $this->getRequest()->getParam('username');
        $result = $this->faqRepository->submitNewQuestion(
            $question,
            $productId,
            $customer
        );

        return $result;
    }
}
