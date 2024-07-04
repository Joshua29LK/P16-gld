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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Vote extends AbstractAjax
{
    /**
     * Get json data
     *
     * @return array|mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getJsonData()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Formkey invalidated'));
        }
        $type = $this->getRequest()->getParam('type');
        $faqId = $this->getRequest()->getParam('faqId');
        $result = $this->faqRepository->updateVote($type, $faqId);
        if ($result) {
            $this->messageManager->addSuccessMessage($result);
        }
        return $this->faqRepository->getFaqVoting($faqId);
    }
}
