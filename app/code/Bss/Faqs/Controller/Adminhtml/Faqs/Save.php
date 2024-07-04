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
namespace Bss\Faqs\Controller\Adminhtml\Faqs;

use Bss\Faqs\Helper\Data as HelperData;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

class Save extends Index
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var \Bss\Faqs\Model\FaqsStoreFactory
     */
    private $faqsStoreFactory;

    /**
     * Save constructor.
     * @param Context $context
     * @param \Bss\Faqs\Model\FaqsFactory $faqFactory
     * @param \Bss\Faqs\Model\FaqsStoreFactory $faqsStoreFactory
     * @param HelperData $helperData
     */
    public function __construct(
        Context $context,
        \Bss\Faqs\Model\FaqsFactory $faqFactory,
        \Bss\Faqs\Model\FaqsStoreFactory $faqsStoreFactory,
        HelperData $helperData
    ) {
        $this->helperData = $helperData;
        parent::__construct($context, $faqFactory);
        $this->faqsStoreFactory = $faqsStoreFactory;
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
                $faqModel = $this->faqFactory->create();
                if ($data['faq_id']) {
                    $faqModel->load($data['faq_id']);
                } else {
                    unset($data['faq_id']);
                }
                $data['frontend_label'] = $this->helperData->returnJson()->serialize($data['frontend_label']);
                $faqModel->setData($data)->save();
                foreach ($data['store_id'] as $storeId) {
                    $faqsStore = $this->faqsStoreFactory->create();
                    $faqsStore->setData('faq_id', $faqModel->getId());
                    $faqsStore->setData('store_id', $storeId);
                    $faqsStore->setData('url_key', $data['url_key']);
                    $faqsStore->save();
                }
                $this->messageManager->addSuccessMessage(__('The FAQ has been saved.'));
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
        return $this->_authorization->isAllowed('Bss_Faqs::faqs_save');
    }
}
