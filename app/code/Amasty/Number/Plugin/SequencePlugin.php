<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Order Number for Magento 2
 */

namespace Amasty\Number\Plugin;

use Amasty\Number\Model\ConfigProvider;
use Amasty\Number\Model\Number\Format\CounterFormatter;
use Amasty\Number\Model\Number\Generator;
use Amasty\Number\Model\ScopeResolver;
use Amasty\Number\Model\SequenceStorage;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DB\Sequence\SequenceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class SequencePlugin
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SequenceStorage
     */
    private $sequenceStorage;

    /**
     * @var ScopeResolver
     */
    private $scopeResolver;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CounterFormatter
     */
    private $counterFormatter;

    public function __construct(
        Generator $generator,
        ConfigProvider $configProvider,
        LoggerInterface $logger,
        SequenceStorage $sequenceStorage,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ScopeResolver $scopeResolver,
        CounterFormatter $counterFormatter
    ) {
        $this->generator = $generator;
        $this->configProvider = $configProvider;
        $this->logger = $logger;
        $this->sequenceStorage = $sequenceStorage;
        $this->scopeResolver = $scopeResolver;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->counterFormatter = $counterFormatter;
    }

    /**
     * After get order's increment ID
     *
     * @param SequenceInterface $subject
     * @param $incrementId
     * @return string
     */
    public function afterGetNextValue(
        SequenceInterface $subject,
        $incrementId
    ) {
        if ($this->configProvider->isEnabled($this->scopeResolver->getStoreId())
            && $this->sequenceStorage->getEntityType() == ConfigProvider::ORDER_TYPE
        ) {
            try {
                $incrementId = $this->generator->getNextFormattedNumber(ConfigProvider::ORDER_TYPE);

                if ($this->counterFormatter->getIsCounterAlreadyReset()) {
                    $incrementId = $this->getNewIncrementId($incrementId);
                }
            } catch (\Throwable $e) {
                $this->logger->critical($e);
            }
        }

        return $incrementId;
    }

    private function getNewIncrementId($incrementId): string
    {
        try {
            $criteria = $this->searchCriteriaBuilder
                ->addFilter(OrderInterface::INCREMENT_ID, $incrementId)
                ->create();
            $orders = $this->orderRepository->getList($criteria);

            if ($orders->getSize()) {
                $incrementId = $this->generator->getNextFormattedNumber(ConfigProvider::ORDER_TYPE);
                return $this->getNewIncrementId($incrementId);
            }
        } catch (\Throwable $e) {
            $this->logger->critical($e);
        }

        return $incrementId;
    }
}
