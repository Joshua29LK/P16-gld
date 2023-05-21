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

namespace Mageprince\Productattach\Api\Data;

interface FileiconSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get productattach list.
     * @return \Mageprince\Productattach\Api\Data\FileiconInterface[]
     */
    public function getItems();

    /**
     * Set test list.
     * @param \Mageprince\Productattach\Api\Data\FileiconInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
