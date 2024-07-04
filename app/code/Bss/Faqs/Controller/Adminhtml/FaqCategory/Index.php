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

use Bss\Faqs\Model\FaqCategoryFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Bss\Faqs\Model\FaqCategoryFactory
     */
    protected $faqCategoryFactory;

    /**
     * Save constructor.
     * @param Context $context
     * @param FaqCategoryFactory $faqCategoryFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Bss\Faqs\Model\FaqCategoryFactory $faqCategoryFactory
    ) {
        parent::__construct($context);
        $this->faqCategoryFactory = $faqCategoryFactory;
    }

    /**
     * Execute
     *
     * @return Page|Page|ResultInterface&ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Bss_Faqs::faqs_faqcategory');
        $resultPage->getConfig()->getTitle()->prepend(__('FAQs Category'));
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
