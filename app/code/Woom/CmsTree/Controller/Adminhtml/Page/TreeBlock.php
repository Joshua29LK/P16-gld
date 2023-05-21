<?php

namespace Woom\CmsTree\Controller\Adminhtml\Page;

use Woom\CmsTree\Controller\Adminhtml\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Woom\CmsTree\Block\Adminhtml\Page\TreeBlock as Tree;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class TreeBlock extends Page
{
    /**
     * Tree Action
     * Retrieve page tree
     *
     * @return ResultInterface
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $pageId = $this->getParam('page_id');
        $storeId = $this->getParam('store', 0);

        if ($storeId) {
            if (!$pageId) {
                $store = $this->storeManager->getStore($storeId);
                $pageId = $store->getRootCmsTreeId();
            }
        }

        $this->initPageAndTree($pageId, $storeId);
        $page = $this->getCurrentPage();
        if (!$page) {
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('cmstree/*/', ['_current' => true, 'page_id' => null]);
        }

        /** @var Tree $block */
        $block = $this->layoutFactory->create()->createBlock(Tree::class);
        $root = $block->getRoot();
        /** @var Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData(
            [
                'data'       => $block->getTree(),
                'parameters' => [
                    'text'         => $block->buildNodeName($root),
                    'draggable'    => false,
                    'allowDrop'    => (bool)$root->getIsVisible(),
                    'page_id'      => (int)$root->getId(),
                    'expanded'     => (int)$block->isTreeExpanded(),
                    'store_id'     => (int)$block->getStore()->getId(),
                    'root_visible' => (int)$root->getIsVisible(),
                ],
            ]
        );
    }
}
