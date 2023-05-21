<?php

namespace Woom\CmsTree\Observer;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Woom\CmsTree\Api\Data\TreeInterface;
use Woom\CmsTree\Api\TreeRepositoryInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\Tree\Node;
use Woom\CmsTree\Model\Page\Source\MenuAddType;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

class AddTreeToTopmenu implements ObserverInterface
{
    /**
     * Core registry
     *
     * @var Registry
     */
    private $registry;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * CMS Tree repository
     *
     * @var TreeRepositoryInterface
     */
    private $treeRepository;

    /**
     * Search criteria
     *
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * AddTreeToTopmenu constructor.
     *
     * @param Registry                $registry
     * @param StoreManagerInterface   $storeManager
     * @param TreeRepositoryInterface $treeRepository
     * @param SearchCriteriaBuilder   $searchCriteriaBuilder
     */
    public function __construct(
        Registry $registry,
        StoreManagerInterface $storeManager,
        TreeRepositoryInterface $treeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->treeRepository = $treeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Add CMS to topmenu
     *
     *
     * @param Observer $observer
     *
     * @return void
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var Node $menu */
        $menu = $observer->getMenu();
        $tree = $menu->getTree();
        $nodeMap = $menu->getAllChildNodes();

        $this->searchCriteriaBuilder->addFilter(
            TreeInterface::IS_IN_MENU,
            1
        )->addFilter(
            PageInterface::IS_ACTIVE,
            1
        )->addFilter(
            'store_id',
            $this->storeManager->getStore()->getId()
        );

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $treeCollection = $this->treeRepository->getList($searchCriteria)->getItems();

        /** @var TreeInterface $treeItem */
        foreach ($treeCollection as $treeItem) {
            $categoryNodeId = 'category-node-' . $treeItem->getMenuAddCategoryId();
            if (array_key_exists($categoryNodeId, $nodeMap)) {
                /** @var Node $sibling */
                $sibling = $nodeMap[$categoryNodeId];

                /** @var Node $parent */
                $parent = $sibling->getParent();

                //append cms menu
                $parentChildren = $parent->getChildren()->getNodes();
                foreach ($parentChildren as $parentChild) {
                    //remove existing child
                    $parent->removeChild($parentChild);

                    //add cms menu, if before existing child
                    if ($treeItem->getMenuAddType() == MenuAddType::BEFORE) {
                        if ($parentChild->getId() == $categoryNodeId) {
                            $this->addCmsTreeToMenu($tree, $treeItem, $parent);
                            $parent->setHasActive(true);
                        }
                    }

                    //return existing child
                    $parent->addChild($parentChild);

                    //add cms menu, if after existing child
                    if ($treeItem->getMenuAddType() == MenuAddType::AFTER) {
                        if ($parentChild->getId() == $categoryNodeId) {
                            $this->addCmsTreeToMenu($tree, $treeItem, $parent);
                            $parent->setHasActive(true);
                        }
                    }
                }
            }
        }
    }

    /***
     * Add CMS tree to menu
     * includes children that are set to appear in menu
     *
     * @param Node $tree
     * @param TreeInterface $treeItem
     * @param Node $parent
     *
     * @return void
     * @throws NoSuchEntityException
     */
    private function addCmsTreeToMenu($tree, $treeItem, $parent)
    {
        $menuNodeData = $this->getMenuNodeData($treeItem);

        if ($menuNodeData) {
            $cmsTreeNode = new \Magento\Framework\Data\Tree\Node(
                $menuNodeData,
                'id',
                $tree->getTree(),
                $parent
            );
            $parent->addChild($cmsTreeNode);
            $cmsTreeParent = $parent->getLastChild();
            $treeItemChildren = $treeItem->getDirectChildren();
            foreach ($treeItemChildren as $childTreeItem) {
                $this->addCmsTreeToMenu($tree, $childTreeItem, $cmsTreeParent);
            }
        }
    }

    /**
     * Get data for menu node generation
     *
     * @param TreeInterface $treeItem
     *
     * @return array
     * @throws NoSuchEntityException
     */
    private function getMenuNodeData($treeItem)
    {
        $menuNodeData = [];
        if ($treeItem->isInMenu()) {
            $isActive = $this->isPageActive($treeItem->getPageId());

            $menuNodeData = [
                'name'      => $treeItem->getMenuLabel() ?: $treeItem->getTitle(),
                'id'        => 'cms-tree-node-' . $treeItem->getPageId(),
                'url'       => $treeItem->getUrl($this->storeManager->getStore()),
                'is_active' => $isActive,
            ];
        }

        return $menuNodeData;
    }

    /**
     * Check if page is active in menu
     *
     * @param int $pageId
     *
     * @return bool
     */
    private function isPageActive($pageId)
    {
        $currentPageId = null;
        $tree = $this->registry->registry('current_cms_tree');
        if ($tree && $tree->getPageId()) {
            $currentPageId = $tree->getPageId();
        }

        $isActive = false;
        if ($pageId == $currentPageId) {
            $isActive = true;
        }

        return $isActive;
    }
}
