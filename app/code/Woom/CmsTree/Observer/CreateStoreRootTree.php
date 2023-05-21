<?php

namespace Woom\CmsTree\Observer;

use Magento\Framework\Event\ObserverInterface;
use Woom\CmsTree\Api\Data\TreeInterface;
use Woom\CmsTree\Api\TreeRepositoryInterface;
use Woom\CmsTree\Model\Page\TreeFactory;
use Woom\CmsTree\Model\Page\Tree;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Exception\LocalizedException;

class CreateStoreRootTree implements ObserverInterface
{
    /**
     * Tree model repository
     *
     * @var TreeRepositoryInterface
     */
    private $treeRepository;

    /**
     * Tree model factory
     *
     * @var TreeFactory
     */
    private $treeFactory;

    /**
     * CreateStoreRootTree constructor.
     *
     * @param TreeRepositoryInterface $treeRepository
     * @param TreeFactory             $treeFactory
     */
    public function __construct(
        TreeRepositoryInterface $treeRepository,
        TreeFactory $treeFactory
    ) {
        $this->treeRepository = $treeRepository;
        $this->treeFactory = $treeFactory;
    }

    /**
     * Create root CMS tree after store is created
     *
     * @param EventObserver $observer
     *
     * @return $this
     * @throws LocalizedException
     */
    public function execute(EventObserver $observer)
    {
        $store = $observer->getData('store');

        //create tree information only if store does not have value in root_cms_tree column set
        if (!$store->getData(TreeInterface::ROOT_CMS_TREE_ID_COLUMN)) {
            /** @var Tree $tree */
            $tree = $this->treeFactory->create();
            $tree->setParentId(TreeInterface::TREE_ROOT_ID);
            $tree->setTitle($store->getName());
            $tree->setPath(TreeInterface::TREE_ROOT_ID);
            $tree->setPosition($store->getId());
            $tree->setLevel(1);
            $tree->setMenuLabel(null);
            $tree->setIsIsMenu(false);
            $tree = $this->treeRepository->save($tree);

            $store->setData(TreeInterface::ROOT_CMS_TREE_ID_COLUMN, $tree->getId());
            $store->getResource()->save($store);
        }

        return $this;
    }
}
