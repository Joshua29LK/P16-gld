<?php

namespace Woom\CmsTree\Controller\Adminhtml\Page;

use Woom\CmsTree\Controller\Adminhtml\Page;
use Magento\Framework\View\Element\Messages;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\Json;

class Move extends Page
{
    /**
     * Move page action
     *
     * @return Json
     */
    public function execute()
    {
        $treeId = $this->getParam('page_id');

        /**
         * New parent page identifier
         */
        $parentNodeId = $this->getParam('pid');

        /**
         * Tree id after which we have put our tree
         */
        $prevNodeId = $this->getParam('paid');

        /** @var $block Messages */
        $block = $this->layoutFactory->create()->getMessagesBlock();
        $error = false;

        try {
            $this->initOnlyTree($treeId);
            $tree = $this->getCurrentTree();
            if ($tree === false) {
                throw new LocalizedException(__('Page is not available for requested store.'));
            }
            //if node is is string (only in case node has no page_id, i.e. root node)
            if ($parentNodeId && !is_numeric($parentNodeId)) {
                $parentNodeId = $this->storeManager->getStore()->getRootCmsTreeId();
            }
            $tree->move($parentNodeId, $prevNodeId);
        } catch (AlreadyExistsException $e) {
            $error = true;
            $this->messageManager->addError(__('There was a page move error. %1', $e->getMessage()));
        } catch (LocalizedException $e) {
            $error = true;
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $error = true;
            $this->messageManager->addError(__('There was a page move error.'));
            $this->logger->critical($e);
        }

        if (!$error) {
            $this->messageManager->addSuccess(__('You moved the page'));
        }

        $block->setMessages($this->messageManager->getMessages(true));
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData(
            [
                'messages' => $block->getGroupedHtml(),
                'error'    => $error
            ]
        );
    }
}
