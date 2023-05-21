<?php

namespace Woom\CmsTree\Controller\Adminhtml\Page;

use Woom\CmsTree\Controller\Adminhtml\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\Page as ResultPage;

class Add extends Page
{
    /**
     * @return Redirect|ResultPage
     */
    public function execute()
    {
        $parentId = $this->getParam('parent');
        $pageId = $this->getParam('page_id');
        $storeId = $this->getParam('store', 0);

        $this->initPageAndTree($pageId, $storeId);
        $page = $this->getCurrentPage();
        $tree = $this->getCurrentTree();

        if (!$page || !$parentId || $page->getId()) {
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->redirectFactory->create();

            return $resultRedirect->setPath('cmstree/*/', ['_current' => true, 'page_id' => null]);
        }

        /**
         * Check if there are data in session (if there was an exception on saving page)
         */
        $pageData = $this->_getSession()->getPageData(true);
        if (is_array($pageData)) {
            $page->addData($pageData);
        }
        if (is_array($pageData)) {
            $tree->addData($pageData);
        }

        /** @var ResultPage $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $resultPage->getConfig()->getTitle()->prepend(__('Pages'));
        $resultPage->getConfig()->getTitle()->prepend(__('New Page'));
        $resultPage->setActiveMenu('Magento_Cms::cms_page');
        $resultPage->addBreadcrumb(__('CMS'), __('CMS'));
        $resultPage->addBreadcrumb(
            __('Manage Pages'),
            __('Manage Pages')
        );

        return $resultPage;
    }
}
