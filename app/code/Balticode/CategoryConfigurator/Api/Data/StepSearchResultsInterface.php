<?php

namespace Balticode\CategoryConfigurator\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface StepSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return StepInterface[]
     */
    public function getItems();

    /**
     * @param StepInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
