<?php

namespace Woom\CmsTree\Observer;

use Magento\Framework\Event\ObserverInterface;
use Woom\CmsTree\Api\Data\TreeInterface;
use Woom\CmsTree\Api\TreeRepositoryInterface;
use Woom\CmsTree\Model\Page\TreeFactory;
use Woom\CmsTree\Model\Page\Tree;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class DeleteStoreRootTree implements ObserverInterface
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
     * Delete root CMS tree after store is deleted
     *
     * @param EventObserver $observer
     *
     * @return $this
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(EventObserver $observer)
    {
        $store = $observer->getData('store');
        $treeId = $store->getData(TreeInterface::ROOT_CMS_TREE_ID_COLUMN);

        /** @var Tree $tree */
        $this->treeRepository->deleteById($treeId);

        return $this;
    }
}
