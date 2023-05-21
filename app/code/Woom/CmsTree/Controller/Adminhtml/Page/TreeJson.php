<?php

namespace Woom\CmsTree\Controller\Adminhtml\Page;

use Woom\CmsTree\Controller\Adminhtml\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\Result\Json;
use Woom\CmsTree\Block\Adminhtml\Page\TreeBlock as TreeAdminBlock;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class TreeJson extends Page
{
    /**
     * Get tree node (Ajax version)
     *
     * @return ResultInterface
     *                        
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        if ($this->getParam('expand_all')) {
            $this->session->setIsTreeExpanded(true);
        } else {
            $this->session->setIsTreeExpanded(false);
        }
        $treeNodeId = $this->getParam('id', false) ?: $this->getParam('page_id');
        $storeId = $this->getParam('store', 0);
        if ($treeNodeId) {
            $this->initPageAndTree(null, $storeId, $treeNodeId);
            $tree = $this->getCurrentTree();
            if (!$tree) {
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('cmstree/*/', ['_current' => true, 'page_id' => null]);
            }
            /** @var Json $resultJson */
            $resultJson = $this->resultJsonFactory->create();

            /** @var TreeAdminBlock $resultBlock */
            $resultBlock = $this->layoutFactory->create()->createBlock(TreeAdminBlock::class);

            return $resultJson->setJsonData(
                $resultBlock->getTreeJson($tree)
            );
        }
    }
}
