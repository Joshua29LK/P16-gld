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
namespace Bss\Faqs\Controller\Adminhtml\FaqCategory;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Delete extends Index
{
    /**
     * Execute
     *
     * @return void
     */
    public function execute()
    {
        $delId = $this->getRequest()->getParam('faq_category_id', 0);
        if ($delId) {
            try {
                $categoryModel = $this->faqCategoryFactory->create()->load($delId);
                if ($categoryModel->getId()) {
                    $categoryModel->delete();
                } else {
                    throw new NoSuchEntityException(__("Category ID not exist"));
                }
                $this->messageManager->addSuccessMessage(__('You deleted the FAQ Category.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getMessage());
                $this->_redirect('adminhtml/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('We can\'t delete FAQ Category right now.'));
                $this->_redirect('adminhtml/*/edit/', ['id' => $delId]);
                return;
            }
        }
        $this->_redirect('adminhtml/*/');
    }

    /**
     * Is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Bss_Faqs::faqCategory_delete');
    }
}
