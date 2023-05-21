<?php

namespace Woom\CmsTree\Controller\Adminhtml\Page;

use Woom\CmsTree\Controller\Adminhtml\Page;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\DataObject;

class Validate extends Page
{
    /**
     * AJAX category validation action
     *
     * @return Json
     */
    public function execute()
    {
        $response = new DataObject();
        $response->setError(0);

        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($response);
        
        return $resultJson;
    }
}
