<?php

namespace Woom\CmsTree\Controller\Adminhtml\Page;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\MessageInterface;
use Woom\CmsTree\Controller\Adminhtml\Page;
use Magento\Cms\Model\Page as PageModel;
use Magento\Framework\Controller\ResultInterface;
use Magento\Backend\Model\View\Result\Redirect;
use Woom\CmsTree\Model\Page\Tree;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends Page
{
    /**
     * Save page action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $pageId = $this->getParam('page_id');
        $storeId = $this->getParam('store', 0);

        $this->initPageAndTree($pageId, $storeId);
        $page = $this->getCurrentPage();
        $tree = $this->getCurrentTree();
        $oldPageStoreId = $page->getStoreId();

        if (!$page) {
            return $resultRedirect->setPath('cmstree/*/', ['_current' => true, 'page_id' => null]);
        }

        $data = [];
        $data = $this->checkPostValue($data);

        $isNewPage = (!isset($data['page_id']) || !$data['page_id']);
        $storeId = isset($data['store_id']) ? $data['store_id'] : null;
        $parentId = isset($data['parent']) ? $data['parent'] : null;
        if ($data) {
            $data = $this->dataProcessor->filter($data);
            if (isset($data['is_active']) && $data['is_active'] === 'true') {
                $data['is_active'] = PageModel::STATUS_ENABLED;
            }
            if (empty($data['page_id'])) {
                $data['page_id'] = null;
            }

            $page->setData($data);

            $this->_eventManager->dispatch(
                'cms_page_prepare_save',
                ['page' => $page, 'request' => $this->getRequest()]
            );

            if (!$this->dataProcessor->validate($data)) {
                return $resultRedirect->setPath('*/*/edit', ['page_id' => $page->getId(), '_current' => true]);
            }

            $page = $this->checkMenuLabel($page);

            try {
                $page->save();

                //if single store mode, we don't have switches from one store to another
                $singleStoreMode = $this->storeManager->isSingleStoreMode() || $this->storeManager->hasSingleStore();
                if ($singleStoreMode) {
                    $this->saveTree($tree, $page, $parentId, $storeId, $isNewPage);
                    $this->messageManager->addSuccess(__('You saved the page.'));
                }

                //if multi store mode, we can have a switch from one store to another
                if (!$singleStoreMode) {
                    $singleStoreId = is_array($storeId) ? reset($storeId) : $storeId;
                    if (!$isNewPage && $oldPageStoreId !== $storeId) {
                        //if this is an existing page, and admin wants to switch it from one store to another
                        $newParentTree = $this->getParentTree(null, $singleStoreId);
                        $tree->getResource()->changeParent($tree, $newParentTree);
                        foreach ($tree->getChildPageIds() as $childPageId) {
                            $this->pageRepository->getById($childPageId)->setStoreId($singleStoreId)->save();
                        }
                        $this->messageManager->addSuccess(__('You saved and moved the page to another store.'));
                    } else {
                        //if this is a new page, or existing page, and store parameter remains the same
                        $this->saveTree($tree, $page, $parentId, $singleStoreId, $isNewPage);
                        $this->messageManager->addSuccess(__('You saved the page.'));
                    }
                }
            } catch (AlreadyExistsException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->logger->critical($e);
                $this->_getSession()->setPageData($data);
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->logger->critical($e);
                $this->_getSession()->setPageData($data);
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong while saving the page.'));
                $this->logger->critical($e);
                $this->_getSession()->setPageData($data);
            }
        }

        $hasError = (bool)$this->messageManager->getMessages()->getCountByType(
            MessageInterface::TYPE_ERROR
        );

        $this->dataPersistor->set('cms_page', $data);

        $redirectParams = $this->getRedirectParams($isNewPage, $hasError, $page->getId(), $parentId, $storeId);

        return $resultRedirect->setPath(
            $redirectParams['path'],
            $redirectParams['params']
        );
    }

    /**
     * Check if a post value exists
     *
     * @param $data
     *
     * @return array
     */
    private function checkPostValue($data)
    {
        if ($this->getRequest()->getPostValue()) {
            return $data = $this->getRequest()->getPostValue();
        }
        return $data;
    }

    /**
     * Check if menu label exists
     *
     * @param $page
     *
     * @return mixed
     */
    private function checkMenuLabel($page)
    {
        if (!$page->getMenuLabel()) {
            return $page->setMenuLabel($page->getTitle());
        }
        return $page;
    }

    /**
     * Save tree object
     *
     * @param Tree      $tree
     * @param PageModel $page
     * @param int       $parentId
     * @param int       $storeId
     * @param bool      $isNewPage
     *
     * @throws NoSuchEntityException
     */
    private function saveTree($tree, $page, $parentId, $storeId, $isNewPage)
    {
        if ($isNewPage) {
            $parentTree = $this->getParentTree($parentId, $storeId);
            $tree->setPath($parentTree->getPath());
            $tree->setParentId($parentTree->getId());
        }
        $tree->setPageId($page->getId());
        $tree->setIdentifier($page->getIdentifier());
        $tree->setTitle($page->getTitle());
        $tree->setIsIsMenu($page->getIsInMenu());
        $tree->setMenuAddType($page->getMenuAddType());
        $tree->setMenuAddCategoryId($page->getMenuAddCategoryId());
        $tree->setMenuLabel($page->getMenuLabel());
        $tree->setHasDataChanges(true);
        $tree->save();
    }

    /**
     * Get parent tree object
     *
     * @param int $parentId
     * @param int $storeId
     *
     * @return Tree
     * @throws NoSuchEntityException
     */
    private function getParentTree($parentId, $storeId)
    {
        if (!$parentId) {
            if (!$storeId) {
                $storeId = 0;
            }
            $parentId = $this->storeManager->getStore($storeId)->getRootCmsTreeId();
        }

        $parentTree = $this->treeRepository->getById($parentId);

        return $parentTree;
    }

    /**
     * Get category redirect path
     *
     * @param bool $isNewPage
     * @param bool $hasError
     * @param int  $pageId
     * @param int  $parentId
     * @param int  $storeId
     *
     * @return array
     */
    private function getRedirectParams($isNewPage, $hasError, $pageId, $parentId, $storeId)
    {
        $params = ['_current' => true];
        if ($storeId) {
            $params['store'] = $storeId;
        }
        if ($isNewPage && $hasError) {
            $path = 'cmstree/*/add';
            $params['parent'] = $parentId;
        } else {
            $path = 'cmstree/*/edit';
            $params['page_id'] = $pageId;
        }

        return ['path' => $path, 'params' => $params];
    }
}
