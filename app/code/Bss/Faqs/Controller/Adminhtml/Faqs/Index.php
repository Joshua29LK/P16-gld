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

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Bss\Faqs\Model\FaqsFactory
     */
    protected $faqFactory;

    /**
     * @param Context $context
     * @param \Bss\Faqs\Model\FaqsFactory $faqFactory
     */
    public function __construct(
        Context $context,
        \Bss\Faqs\Model\FaqsFactory $faqFactory
    ) {
        parent::__construct($context);
        $this->faqFactory = $faqFactory;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page&\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Bss_Faqs::faqs_question');
        $resultPage->getConfig()->getTitle()->prepend(__('FAQs Question'));
        return $resultPage;
    }

    /**
     * Is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Bss_Faqs::config');
    }
}
