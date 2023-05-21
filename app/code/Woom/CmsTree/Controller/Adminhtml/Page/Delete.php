<?php

namespace Woom\CmsTree\Controller\Adminhtml\Page;

use Woom\CmsTree\Controller\Adminhtml\Page;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\Model\View\Result\Redirect;

class Delete extends Page
{
    /**
     * Delete page action
     *
     * @return Redirect
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $pageId = $this->getParam('page_id');
        $storeId = $this->getParam('store', 0);
        $parentId = null;
        if ($pageId) {
            try {
                $this->initPageAndTree($pageId, $storeId);
                $page = $this->getCurrentPage();
                $tree = $this->getCurrentTree();
                $parentId = $tree->getParentId();
                $this->_eventManager->dispatch('cms_controller_page_delete', ['page' => $page]);
                $this->pageRepository->delete($page);
                $this->treeRepository->delete($tree);
                $this->messageManager->addSuccess(__('You deleted the page.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());

                return $resultRedirect->setPath('cmstree/*/edit', ['_current' => true]);
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong while trying to delete the page.'));

                return $resultRedirect->setPath('cmstree/*/edit', ['_current' => true]);
            }
        }

        return $resultRedirect->setPath('cmstree/*/', ['_current' => true, 'page_id' => $parentId]);
    }
}
