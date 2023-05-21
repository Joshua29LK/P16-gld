<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Custom Order Number for Magento 2
*/

namespace Amasty\Number\Plugin;

use Amasty\Number\Model\ConfigProvider;
use Amasty\Number\Model\Number\Generator;
use Amasty\Number\Model\ScopeResolver;
use Amasty\Number\Model\SequenceStorage;
use Magento\Framework\DB\Sequence\SequenceInterface;
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

    public function __construct(
        Generator $generator,
        ConfigProvider $configProvider,
        LoggerInterface $logger,
        SequenceStorage $sequenceStorage,
        ScopeResolver $scopeResolver
    ) {
        $this->generator = $generator;
        $this->configProvider = $configProvider;
        $this->logger = $logger;
        $this->sequenceStorage = $sequenceStorage;
        $this->scopeResolver = $scopeResolver;
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
            } catch (\Throwable $e) {
                $this->logger->critical($e);
            }
        }

        return $incrementId;
    }
}
