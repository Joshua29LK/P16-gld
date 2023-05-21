<?php

namespace Balticode\CategoryConfigurator\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Registry;

abstract class Step extends Action
{
    const ADMIN_RESOURCE = 'Balticode_CategoryConfigurator::top_level';

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * @param Page $resultPage
     * @return Page
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Balticode'), __('Balticode'))
            ->addBreadcrumb(__('Step'), __('Step'));

        return $resultPage;
    }
}
