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

class Search extends AbstractAjax
{
    /**
     * Get json data
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getJsonData()
    {
        $categoryId = $this->getRequest()->getParam('category');
        $keyword = $this->getRequest()->getParam('keyword');
        $sort = $this->getRequest()->getParam('sort_order', 'time');
        $result = $this->faqRepository->getSearchData($keyword, $categoryId, $sort);
        $result['sidebar'] = $this->faqRepository->getSidebarData();
        return $result;
    }
}
