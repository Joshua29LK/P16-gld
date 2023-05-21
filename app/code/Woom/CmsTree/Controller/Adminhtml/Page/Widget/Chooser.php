<?php

namespace Woom\CmsTree\Controller\Adminhtml\Page\Widget;

use Woom\CmsTree\Controller\Adminhtml\Page\Widget;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\Result\Raw;

class Chooser extends Widget
{
    /**
     * Raw result factory
     *
     * @var RawFactory
     */
    private $resultRawFactory;

    /**
     * Chooser constructor.
     *
     * @param Context       $context
     * @param LayoutFactory $layoutFactory
     * @param RawFactory    $resultRawFactory
     */
    public function __construct(
        Context $context,
        LayoutFactory $layoutFactory,
        RawFactory $resultRawFactory
    ) {
        parent::__construct($context, $layoutFactory);
        $this->resultRawFactory = $resultRawFactory;
    }

    /**
     * Chooser Source action
     *
     * @return Raw
     */
    public function execute()
    {
        /** @var Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();

        //resolve store id from referer and set it as parameter
        $referer = explode('/', $this->_redirect->getRefererUrl());
        $storeKey = array_search('store', $referer);
        if ($storeKey && array_key_exists($storeKey + 1, $referer)) {
            $storeId = $referer[$storeKey + 1];
            $this->_request->setParams(array_merge($this->_request->getParams(), ['store' => $storeId]));
        }

        return $resultRaw->setContents($this->getCmsTreeBlock()->toHtml());
    }
}
