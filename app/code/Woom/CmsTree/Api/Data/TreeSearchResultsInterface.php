<?php

namespace Woom\CmsTree\Api\Data;

/**
 * @api
 */
interface TreeSearchResultsInterface
{
    /**
     * Get tree list.
     *
     * @return \Woom\CmsTree\Api\Data\TreeInterface[]
     */
    public function getItems();

    /**
     * Set tree list.
     *
     * @param \Woom\CmsTree\Api\Data\TreeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
