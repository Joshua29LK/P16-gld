<?php

namespace Balticode\CategoryConfigurator\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface ConfiguratorSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return ConfiguratorInterface[]
     */
    public function getItems();

    /**
     * @param ConfiguratorInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
