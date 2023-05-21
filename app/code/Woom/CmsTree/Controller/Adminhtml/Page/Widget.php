<?php

namespace Woom\CmsTree\Controller\Adminhtml\Page;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Element\BlockInterface;
use Woom\CmsTree\Block\Adminhtml\Page\Widget\Chooser;

abstract class Widget extends Action
{
    /**
     * Layout factory
     *
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * Widget constructor.
     *
     * @param Context       $context
     * @param LayoutFactory $layoutFactory
     */
    public function __construct(
        Context $context,
        LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * Get CMS tree block
     *
     * @return BlockInterface
     */
    public function getCmsTreeBlock()
    {
        return $this->layoutFactory->create()->createBlock(
            Chooser::class,
            '',
            [
                'data' => [
                    'id' => $this->getRequest()->getParam('uniq_id'),
                    'store' => $this->getRequest()->getParam('store')
                ]
            ]
        );
    }
}
