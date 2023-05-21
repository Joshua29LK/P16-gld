<?php

namespace Mageprince\Productattach\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for attachment search results.
 * @api
 */
interface AttachmentSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get attachment list.
     *
     * @return ProductattachTableInterface[] | ProductattachTableInterface[]
     */
    public function getItems();

    /**
     * Set attachment list.
     *
     * @param ProductattachTableInterface[] $items
     * @return AttachmentSearchResultsInterface
     */
    public function setItems(array $items);
}
