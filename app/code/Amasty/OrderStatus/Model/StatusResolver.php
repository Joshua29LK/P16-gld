<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Status for Magento 2
 */

namespace Amasty\OrderStatus\Model;

use Amasty\OrderStatus\Model\ResourceModel\Status\Collection;
use Amasty\OrderStatus\Model\ResourceModel\Status\CollectionFactory;

class StatusResolver
{
    public const STATUS_SEPARATOR = '_';

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var array
     */
    private $statuses = [];

    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    public function getStatusId(string $status): ?int
    {
        if (array_key_exists($status, $this->statuses)) {
            return $this->statuses[$status];
        }

        $statusId = null;
        if (strpos($status, self::STATUS_SEPARATOR) !== false) {
            list($state, $alias) = explode(self::STATUS_SEPARATOR, $status, 2);
            $statusId = $this->getIdByStateAndAlias($state, $alias);
        }

        return $this->statuses[$status] = $statusId;
    }

    private function getIdByStateAndAlias(string $state, string $alias): ?int
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect($collection->getIdFieldName())
            ->addFieldToFilter('alias', $alias)
            ->addFieldToFilter(
                'parent_state',
                [
                    ['finset' => $state],
                    ['eq' => '']
                ]
            )->setOrder($collection->getIdFieldName())
            ->setPageSize(1);

        $statusId = $collection->getFirstItem()->getId();

        return $statusId ? (int)$statusId : null;
    }
}
