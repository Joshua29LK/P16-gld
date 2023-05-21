<?php

/**
 * MagePrince
 * Copyright (C) 2020 Mageprince <info@mageprince.com>
 *
 * @package Mageprince_Productattach
 * @copyright Copyright (c) 2020 Mageprince (http://www.mageprince.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MagePrince <info@mageprince.com>
 */

namespace Mageprince\Productattach\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Mageprince\Productattach\Api\Data\FileiconInterface;
use Mageprince\Productattach\Model\ResourceModel\Fileicon as ResourceFileicon;
use Magento\Framework\Reflection\DataObjectProcessor;
use Mageprince\Productattach\Api\Data\FileiconSearchResultsInterfaceFactory;
use Mageprince\Productattach\Model\ResourceModel\Fileicon\CollectionFactory as FileiconCollectionFactory;
use Magento\Framework\Api\SortOrder;
use Mageprince\Productattach\Api\FileiconRepositoryInterface;
use Mageprince\Productattach\Api\Data\FileiconInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\DataObjectHelper;

class FileiconRepository implements fileiconRepositoryInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var FileiconSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var ResourceFileicon
     */
    protected $resource;

    /**
     * @var FileiconCollectionFactory
     */
    protected $fileiconCollectionFactory;

    /**
     * @var FileiconFactory
     */
    protected $fileiconFactory;

    /**
     * @var FileiconInterfaceFactory
     */
    protected $dataFileiconFactory;

    /**
     * FileiconRepository constructor.
     *
     * @param ResourceFileicon $resource
     * @param FileiconFactory $fileiconFactory
     * @param FileiconInterfaceFactory $dataFileiconFactory
     * @param FileiconCollectionFactory $fileiconCollectionFactory
     * @param FileiconSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceFileicon $resource,
        FileiconFactory $fileiconFactory,
        FileiconInterfaceFactory $dataFileiconFactory,
        FileiconCollectionFactory $fileiconCollectionFactory,
        FileiconSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->fileiconFactory = $fileiconFactory;
        $this->fileiconCollectionFactory = $fileiconCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataFileiconFactory = $dataFileiconFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(FileiconInterface $fileicon)
    {
        try {
            $fileicon->getResource()->save($fileicon);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the fileicon: %1',
                $exception->getMessage()
            ));
        }
        return $fileicon;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($fileiconId)
    {
        $fileicon = $this->fileiconFactory->create();
        $fileicon->getResource()->load($fileicon, $fileiconId);
        if (!$fileicon->getId()) {
            throw new NoSuchEntityException(__('Fileicon with id "%1" does not exist.', $fileiconId));
        }
        return $fileicon;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $collection = $this->fileiconCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(FileiconInterface $fileicon)
    {
        try {
            $fileicon->getResource()->delete($fileicon);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Fileicon: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($fileiconId)
    {
        return $this->delete($this->getById($fileiconId));
    }
}
