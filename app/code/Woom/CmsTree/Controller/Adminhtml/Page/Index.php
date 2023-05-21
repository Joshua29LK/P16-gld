<?php

namespace Woom\CmsTree\Controller\Adminhtml\Page;

use Woom\CmsTree\Controller\Adminhtml\Page;
use Magento\Backend\Model\View\Result\Forward;

class Index extends Page
{
    /**
     * Cms tree index action
     *
     * @return Forward
     */
    public function execute()
    {
        /** @var Forward $resultForward */
        $resultForward = $this->forwardFactory->create();

        return $resultForward->forward('edit');
    }
}
