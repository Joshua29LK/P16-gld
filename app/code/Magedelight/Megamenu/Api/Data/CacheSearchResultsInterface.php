<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magedelight\Megamenu\Api\Data;

interface CacheSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Cache list.
     * @return \Magedelight\Megamenu\Api\Data\CacheInterface[]
     */
    public function getItems();

    /**
     * Set name list.
     * @param \Magedelight\Megamenu\Api\Data\CacheInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
