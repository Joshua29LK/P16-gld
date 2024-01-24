<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Status for Magento 2
 */

namespace Amasty\OrderStatus\Model;

use Amasty\OrderStatus\Model\ResourceModel\Status\Collection as StatusCollection;
use Amasty\OrderStatus\Model\ResourceModel\Status\CollectionFactory;
use Magento\Framework\Registry;

class AmOrderStatusRegistry
{
    /**
     * @var array
     */
    private $orderStatusMap = [];

    /**
     * @var CollectionFactory
     */
    private $statusCollectionFactory;

    /**
     * @var Registry
     */
    private $coreRegistry;

    public function __construct(
        CollectionFactory $statusCollectionFactory,
        Registry $coreRegistry
    ) {
        $this->statusCollectionFactory = $statusCollectionFactory;
        $this->coreRegistry = $coreRegistry;
    }

    public function get(string $status): ?Status
    {
        if (!isset($this->orderStatusMap[$status])) {
            $this->register($status);
        }

        return $this->orderStatusMap[$status] ?: null;
    }

    public function register(string $status): void
    {
        if (!isset($this->orderStatusMap[$status])) {
            $this->orderStatusMap[$status] = false;

            /** @var StatusCollection $statusCollection */
            $statusCollection = $this->statusCollectionFactory->create();
            $statusCollection->addFieldToFilter('is_active', ['eq' => 1]);
            $statusCollection->addFieldToFilter('is_system', ['eq' => 0]);

            foreach ($statusCollection as $statusModel) {
                $statusAlias = '_' . $statusModel->getAlias();
                $aliasPosition = strpos($status, $statusAlias);

                if ($aliasPosition && substr($status, $aliasPosition) === $statusAlias) {
                    $this->orderStatusMap[$status] = $statusModel;
                    break;
                }
            }
        }
    }
}
