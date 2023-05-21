<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Archive for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Orderarchive\Model\ResourceModel\Customer\Orders\Grid;

use Amasty\Orderarchive\Model\ArchiveFactory;
use Amasty\Orderarchive\Model\ResourceModel\OrderGrid;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface as Logger;

class Collection extends OrderGrid\Collection
{
    /**
     * @var Registry
     */
    protected $registryManager;

    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        Registry $registryManager,
        $mainTable = ArchiveFactory::ORDER_ARCHIVE_NAMESPACE,
        $resourceModel = OrderGrid::class
    ) {
        $this->registryManager = $registryManager;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $customerId = $this->registryManager->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        $this->addFieldToFilter('customer_id', $customerId);

        return $this;
    }
}
