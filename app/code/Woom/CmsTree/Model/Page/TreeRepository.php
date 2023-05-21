<?php

namespace Woom\CmsTree\Model\Page;

use Woom\CmsTree\Api\TreeRepositoryInterface;
use Woom\CmsTree\Model\ResourceModel\Page\Tree as ResourceTree;
use Woom\CmsTree\Model\Page\TreeFactory;
use Woom\CmsTree\Model\ResourceModel\Page\Tree\CollectionFactory as TreeCollectionFactory;
use Woom\CmsTree\Api\Data\TreeSearchResultsInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Woom\CmsTree\Model\ResourceModel\Page\Tree\Collection as TreeCollection;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SortOrder;
use Woom\CmsTree\Api\Data\TreeInterface;

class TreeRepository implements TreeRepositoryInterface
{
    /**
     * Tree resource
     *
     * @var ResourceTree
     */
    private $resource;

    /**
     * Tree factory
     *
     * @var TreeFactory
     */
    private $treeFactory;

    /**
     * Tree collection factory
     *
     * @var TreeCollectionFactory
     */
    private $treeCollectionFactory;

    /**
     * Tree search results factory
     *
     * @var TreeSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * TreeRepository constructor.
     *
     * @param ResourceTree                      $resource
     * @param TreeFactory                       $treeFactory
     * @param TreeCollectionFactory             $treeCollectionFactory
     * @param TreeSearchResultsInterfaceFactory $searchResultsFactory
     * @param StoreManagerInterface             $storeManager
     */
    public function __construct(
        ResourceTree $resource,
        TreeFactory $treeFactory,
        TreeCollectionFactory $treeCollectionFactory,
        TreeSearchResultsInterfaceFactory $searchResultsFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->treeFactory = $treeFactory;
        $this->treeCollectionFactory = $treeCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Save Tree data
     *
     * @param TreeInterface $tree
     *
     * @return TreeInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function save(TreeInterface $tree)
    {
        if (empty($tree->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $tree->setStoreId($storeId);
        }
        try {
            $this->resource->save($tree);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the tree: %1',
                    $exception->getMessage()
                )
            );
        }

        return $tree;
    }

    /**
     * Load Tree data by given Tree Identity
     *
     * @param string $treeId
     *
     * @return Tree
     * @throws NoSuchEntityException
     */
    public function getById($treeId)
    {
        $tree = $this->treeFactory->create();
        $tree->load($treeId);
        if (!$tree->getId()) {
            throw new NoSuchEntityException(__('Tree with id "%1" does not exist.', $treeId));
        }

        return $tree;
    }

    /**
     * Load Tree data by given Tree Identity
     *
     * @param string $pageId
     *
     * @return Tree
     * @throws NoSuchEntityException
     */
    public function getByPageId($pageId)
    {
        $tree = $this->treeFactory->create();
        $tree->load($pageId, TreeInterface::PAGE_ID);
        if (!$tree->getId()) {
            throw new NoSuchEntityException(__('Tree with page id "%1" does not exist.', $pageId));
        }

        return $tree;
    }

    /**
     * Load Tree data by given request url
     *
     * @param string $url
     * @param int    $storeId
     *
     * @return Tree
     * @throws NoSuchEntityException
     */
    public function getByRequestUrl($url, $storeId)
    {
        $tree = $this->treeFactory->create();
        $tree->getByRequestUrl($url, $storeId);
        if (!$tree->getId()) {
            throw new NoSuchEntityException(__('Tree with page associated request url "%1" does not exist.', $url));
        }

        return $tree;
    }

    /**
     * Load Tree data by given parent tree Identity
     *
     * @param string $parentTreeId
     *
     * @return Tree
     * @throws NoSuchEntityException
     */
    public function getFirstChild($parentTreeId)
    {
        $tree = $this->treeFactory->create();
        $tree->getFirstChild($parentTreeId);
        if (!$tree->getId()) {
            throw new NoSuchEntityException(__('Tree with page parent id "%1" does not exist.', $parentTreeId));
        }

        return $tree;
    }

    /**
     * Load Tree data collection by given search criteria
     *
     * @param SearchCriteriaInterface $criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     *
     * @return TreeCollection
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->treeCollectionFactory->create();
        $collection->addCmsPages();
        $collection->addStoresColumn();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), true);
                    continue;
                }
                if ($filter->getField() === 'is_in_menu') {
                    $collection->addMenuFilter($filter->getValue());
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $trees = [];
        /** @var Tree $treeModel */
        foreach ($collection as $treeModel) {
            $trees[] = $treeModel;
        }
        $searchResults->setItems($trees);
        $searchResults->setTotalCount($collection->count());

        return $searchResults;
    }

    /**
     * Delete Tree
     *
     * @param TreeInterface $tree
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(TreeInterface $tree)
    {
        try {
            $this->resource->delete($tree);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the tree: %1',
                    $exception->getMessage()
                )
            );
        }

        return true;
    }

    /**
     * Delete Tree by given Tree Identity
     *
     * @param string $treeId
     *
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($treeId)
    {
        return $this->delete($this->getById($treeId));
    }
}
