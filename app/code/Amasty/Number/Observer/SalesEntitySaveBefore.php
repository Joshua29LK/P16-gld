<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Order Number for Magento 2
 */

namespace Amasty\Number\Observer;

use Amasty\Number\Model\ConfigProvider;
use Amasty\Number\Model\Number\Generator;
use Amasty\Number\Model\SequenceStorage;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\ResourceModel\Order\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;

class SalesEntitySaveBefore implements ObserverInterface
{
    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var SequenceStorage
     */
    private $sequenceStorage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $collectionFactoryTypes = [];

    public function __construct(
        Generator $generator,
        ConfigProvider $configProvider,
        SequenceStorage $sequenceStorage,
        LoggerInterface $logger,
        $collectionFactoryTypes = []
    ) {
        $this->generator = $generator;
        $this->configProvider = $configProvider;
        $this->sequenceStorage = $sequenceStorage;
        $this->logger = $logger;
        $this->collectionFactoryTypes = $collectionFactoryTypes;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        try {
            $type = $this->getEntityType($observer);
            $entity = $observer->getData($type);

            if (!$entity->isObjectNew()) {
                return null;
            }

            $storeId = (int)$entity->getOrder()->getStore()->getId();

            if ($this->configProvider->isEnabled($storeId)) {
                $this->sequenceStorage->setEntityType($type);
                $this->sequenceStorage->setEntity($entity);
                $this->sequenceStorage->setOrder($entity->getOrder());
                $this->configProvider->setStoreId($storeId);
                $newIncrementId = $this->addUniquePostfix($this->generator->getNextFormattedNumber($type), $type);
                $entity->setIncrementId($newIncrementId);
            }
        } catch (\Throwable $e) {
            $this->logger->critical($e);
        }
    }

    /**
     * Guarantee Invoice, Shipment and Memo increment id uniqueness with "Same as Order format" setting enabled
     *
     * @param string $newIncrementId
     * @param string $type
     * @return string
     */
    private function addUniquePostfix(string $newIncrementId, string $type)
    {
        if (isset($this->collectionFactoryTypes[$type])) {
            /** @var AbstractCollection $collection */
            $collection = $this->collectionFactoryTypes[$type]->create();
            $collection->addFieldToFilter('increment_id', ['like' => $newIncrementId . '%']);

            if ($collection->getSize() !== 0) {
                $newIncrementId .= '-' . $collection->getSize();
            }
        }

        return $newIncrementId;
    }

    /**
     * @param $observer
     * @return string
     */
    private function getEntityType($observer): string
    {
        foreach (ConfigProvider::AVAILABLE_ENTITY_TYPES as $type) {
            if (is_object($observer->getData($type))) {
                return $type;
            }
        }

        return '';
    }
}
