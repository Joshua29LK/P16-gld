<?php

namespace Woom\CmsTree\Controller\Adminhtml\Page;

use Woom\CmsTree\Controller\Adminhtml\Page;
use Magento\Framework\Controller\ResultInterface;
use Magento\Backend\Model\View\Result\Page as ResultPage;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends Page
{
    /**
     * Edit page action
     *
     * @return ResultInterface
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $pageId = $this->getParam('page_id');
        $storeId = $this->getParam('store', 0);
        $parentId = $this->getParam('parent');

        $store = $this->storeManager->getStore($storeId);
        $this->storeManager->setCurrentStore($store->getCode());

        $this->initPageAndTree($pageId, $storeId);
        $page = $this->getCurrentPage();
        $tree = $this->getCurrentTree();

        //after store switch, if page isn't associated to store, switch to root page
        if ($page && $page->getStoreId() !== null && !in_array($storeId, $page->getStoreId())) {
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath(
                'cmstree/*/*',
                ['_current' => true, 'page_id' => 0, 'store' => $storeId]
            );
        }

        //if admin opens a menu, but has no page selected, open root page for that store
        if ($pageId == null && !$parentId) {
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath(
                'cmstree/*/*',
                ['_current' => true, 'page_id' => 0, 'store' => $storeId]
            );
        }

        $pageTitle = 'New Page';
        if ($page && $page->getId()) {
            $pageTitle = $page->getTitle();
        }

        if ((!$page || !$page->getId()) && ($tree && $tree->getId())) {
            $pageTitle = $tree->getTitle();
        }

        /**
         * Check if there are data in session (if there was an exception on saving page)
         */
        $pageData = $this->_getSession()->getPageData(true);
        if (is_array($pageData)) {
            $page->addData($pageData);
        }

        /** @var ResultPage $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Pages'));
        $resultPage->getConfig()->getTitle()->prepend(__($pageTitle));
        $resultPage->setActiveMenu('Magento_Cms::cms_page');
        $resultPage->addBreadcrumb(__('CMS'), __('CMS'));
        $resultPage->addBreadcrumb(__('Manage Pages'), __('Manage Pages'));

        return $resultPage;
    }
}
