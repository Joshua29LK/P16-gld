<?php

namespace Woom\CmsTree\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Woom\CmsTree\Api\TreeRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\Exception\NoSuchEntityException;

class ReplaceCmsWithTree implements ObserverInterface
{
    /**
     * Tree model repository
     *
     * @var TreeRepositoryInterface
     */
    private $treeRepository;

    /**
     * Store manager
     *
     * @var TreeRepositoryInterface
     */
    private $storeManager;

    /**
     * Core registry
     *
     * @var Registry
     */
    private $registry;

    /**
     * ReplaceCmsWithTree constructor.
     *
     * @param TreeRepositoryInterface $treeRepository
     * @param StoreManagerInterface   $storeManager
     * @param Registry                $registry
     */
    public function __construct(
        TreeRepositoryInterface $treeRepository,
        StoreManagerInterface $storeManager,
        Registry $registry
    ) {
        $this->treeRepository = $treeRepository;
        $this->storeManager = $storeManager;
        $this->registry = $registry;
    }

    /**
     * Replace CMS page with CMS page in CMS tree structure
     *
     * @param Observer $observer
     *
     * @return $this
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $condition = $observer->getEvent()->getCondition();
        $identifier = $condition->getIdentifier();
        $storeId = $this->storeManager->getStore()->getId();

        if ($identifier) {
            $identifier = str_replace('.html', '', $identifier);

            try {
                $tree = $this->treeRepository->getByRequestUrl($identifier, $storeId);
                if ($tree && $tree->getPageId()) {
                    $condition->setIdentifier($tree->getPageIdentifier());
                    $this->registry->register('current_cms_tree', $tree);
                }
            } catch (\Exception $e) {
                //redirect to 404 page
                $condition->setContinue(false);
            }
        }

        return $this;
    }
}
