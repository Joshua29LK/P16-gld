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

namespace Mageprince\Productattach\Api;

interface ProductattachInterface
{
    /**
     * Add Update attachment
     *
     * @param int $attachmentId
     * @param string $name
     * @param string $description
     * @param string $url
     * @param string $products
     * @param string $store
     * @param string $customerGroup
     * @param string $active
     * @return \Mageprince\Productattach\Api\Data\ProductattachTableInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function updateInsertAttachment(
        $attachmentId,
        $name,
        $description,
        $url,
        $products,
        $store,
        $customerGroup,
        $active
    );

    /**
     * Delete attachment
     *
     * @param int $attachmentId
     * @return bool
     * @throws \Exception
     */
    public function deleteAttachment($attachmentId);

    /**
     * Get attachment
     *
     * @param int $attachmentId
     * @return \Mageprince\Productattach\Api\Data\ProductattachTableInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAttachment($attachmentId);

    /**
     * Retrieve queue matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Mageprince\Productattach\Api\Data\AttachmentSearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Get attachment by product id
     *
     * @param int $productId
     * @return \Mageprince\Productattach\Api\Data\ProductattachTableInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAttachmentByProductId($productId);
}
