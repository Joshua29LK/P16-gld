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

use Bss\Faqs\Helper\Data as HelperData;
use Magento\Framework\Exception\LocalizedException;

class Save extends Index
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Bss\Faqs\Model\FaqCategoryFactory $faqCategoryFactory
     * @param HelperData $helperData
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Bss\Faqs\Model\FaqCategoryFactory $faqCategoryFactory,
        HelperData $helperData
    ) {
        $this->helperData = $helperData;
        parent::__construct(
            $context,
            $faqCategoryFactory
        );
    }

    /**
     * Execute
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            try {
                if (!$data['faq_category_id']) {
                    unset($data['faq_category_id']);
                }
                $data['frontend_label'] = $this->helperData->returnJson()->serialize($data['frontend_label']);
                $categoryModel = $this->faqCategoryFactory->create();
                $categoryModel->setData($data)->save();
                $this->messageManager->addSuccessMessage(__('The Category %1 has been saved.', $data['title']));
                $this->_redirect('adminhtml/*/');
                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __($e->getMessage()));
            }
        }
        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
    }

    /**
     * Is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Bss_Faqs::faqCategory_save');
    }
}
