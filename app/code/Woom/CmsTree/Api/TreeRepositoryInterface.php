<?php

namespace Woom\CmsTree\Api;

/**
 * @api
 */
interface TreeRepositoryInterface
{
    /**
     * Save tree.
     *
     * @param \Woom\CmsTree\Api\Data\TreeInterface $tree
     *
     * @return \Woom\CmsTree\Api\Data\TreeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Woom\CmsTree\Api\Data\TreeInterface $tree);

    /**
     * Retrieve tree.
     *
     * @param int $treeId
     *
     * @return \Woom\CmsTree\Api\Data\TreeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($treeId);

    /**
     * Retrieve tree by page identifier.
     *
     * @param int $pageId
     *
     * @return \Woom\CmsTree\Api\Data\TreeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByPageId($pageId);

    /**
     * Retrieve tree by request url.
     *
     * @param string $url
     * @param int    $storeId
     *
     * @return \Woom\CmsTree\Api\Data\TreeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByRequestUrl($url, $storeId);

    /**
     * Retrieve first child tree by parent tree identifier.
     *
     * @param int $parentTreeId
     *
     * @return \Woom\CmsTree\Api\Data\TreeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFirstChild($parentTreeId);

    /**
     * Retrieve trees matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Woom\CmsTree\Api\Data\TreeSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete tree.
     *
     * @param \Woom\CmsTree\Api\Data\TreeInterface $tree
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Woom\CmsTree\Api\Data\TreeInterface $tree);

    /**
     * Delete tree by ID.
     *
     * @param int $treeId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($treeId);
}
