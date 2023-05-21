<?php

namespace Woom\CmsTree\Controller\Adminhtml\Page;

use Woom\CmsTree\Controller\Adminhtml\Page;
use Magento\Framework\Controller\Result\Json;

class RefreshPath extends Page
{
    /**
     * Build response for refresh input element 'path' in form
     *
     * @return Json
     */
    public function execute()
    {
        $pageId = $this->getParam('page_id');
        $storeId = $this->getParam('store', 0);

        $this->initPageAndTree($pageId, $storeId);
        $tree = $this->getCurrentTree();

        /** @var Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData(['page_id' => $tree->getPageId(), 'path' => $tree->getPath()]);
    }
}
