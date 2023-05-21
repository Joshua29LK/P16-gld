<?php

namespace Woom\CmsTree\Controller\Adminhtml\Page\Widget;

use Woom\CmsTree\Controller\Adminhtml\Page\Widget;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Registry;
use Magento\Framework\Controller\Result\Json;
use Woom\CmsTree\Api\TreeRepositoryInterface;

class TreeJson extends Widget
{
    /**
     * Core registry
     *
     * @var Registry
     */
    private $coreRegistry;

    /**
     * Tree repository
     *
     * @var TreeRepositoryInterface
     */
    private $treeRepository;

    /**
     * JSON factory
     *
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * TreeJson constructor.
     *
     * @param Context                 $context
     * @param LayoutFactory           $layoutFactory
     * @param JsonFactory             $resultJsonFactory
     * @param Registry                $coreRegistry
     * @param TreeRepositoryInterface $treeRepository
     */
    public function __construct(
        Context $context,
        LayoutFactory $layoutFactory,
        JsonFactory $resultJsonFactory,
        Registry $coreRegistry,
        TreeRepositoryInterface $treeRepository
    ) {
        parent::__construct($context, $layoutFactory);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->coreRegistry = $coreRegistry;
        $this->treeRepository = $treeRepository;
    }

    /**
     * CMS Tree tree node (Ajax version)
     *
     * @return Json
     */
    public function execute()
    {
        $json = null;
        $treeId = (int)$this->getRequest()->getPost('id');
        if ($treeId) {
            $selected = $this->getRequest()->getPost('selected', '');
            try {
                $tree = $this->treeRepository->getById($treeId);
                if ($tree->getId()) {
                    $this->coreRegistry->register('tree', $tree);
                    $this->coreRegistry->register('current_tree', $tree);
                }
                $cmsTreeBlock = $this->getCmsTreeBlock()->setSelectedTrees(explode(',', $selected));
                $json = $cmsTreeBlock->getTreeJson($tree);
            } catch (\Exception $e) {
                //do nothing
            }
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setJsonData($json);
    }
}
