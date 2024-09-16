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

class Question extends AbstractAjax
{
    /**
     * Get json data
     *
     * @return array|\Bss\Faqs\Model\Faqs
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getJsonData()
    {
        $request = $this->getRequest();
        if ($request->getParam('type') == 'id') {
            $result = $this->faqRepository->getFaqById($request->getParam('value'));
        } elseif ($request->getParam('type') == 'url') {
            $result = $this->faqRepository->getFaqByUrl($request->getParam('value'));
        }
        $result['cate_info'] = $this->faqRepository->getCategoryImage($request->getParam('category_id', null));
        return $result;
    }
}
