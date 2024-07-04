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

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Edit extends Action
{
    /**
     * @var \Bss\Faqs\Model\Faqs
     */
    private $faqsModel;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * Edit constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Bss\Faqs\Model\FaqsFactory $faqsModel
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Bss\Faqs\Model\FaqsFactory $faqsModel
    ) {
        parent::__construct($context);
        $this->faqsModel = $faqsModel;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page&\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $faqsId = (int)$this->getRequest()->getParam('faq_id', 0);
        $this->coreRegistry->register('faq_id', $faqsId);
        $headerText = ($faqsId == 0) ? __('Add New FAQ') : __('Edit FAQ');
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend($headerText);
        return $resultPage;
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
