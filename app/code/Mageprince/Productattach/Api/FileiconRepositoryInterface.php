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

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageprince\Productattach\Api\Data\FileiconInterface;
use Mageprince\Productattach\Api\Data\FileiconSearchResultsInterface;

interface FileiconRepositoryInterface
{
    /**
     * Save File icon
     *
     * @param FileiconInterface $fileicon
     * @return FileiconInterface
     * @throws LocalizedException
     */
    public function save(
        FileiconInterface $fileicon
    );

    /**
     * Retrieve File icon
     *
     * @param string $fileiconId
     * @return FileiconInterface
     * @throws LocalizedException
     */
    public function getById($fileiconId);

    /**
     * Retrieve File icon matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return FileiconSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete File icon
     *
     * @param FileiconInterface $fileicon
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(
        FileiconInterface $fileicon
    );

    /**
     * Delete File icon by ID
     *
     * @param string $fileiconId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($fileiconId);
}
