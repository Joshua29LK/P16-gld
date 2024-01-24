<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Status for Magento 2
 */

namespace Amasty\OrderStatus\Model\Order;

use Amasty\OrderStatus\Model\ResourceModel\Status\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigProcessor
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var CollectionFactory
     */
    protected $amastyOrderStatusCollection;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $amastyOrderStatusCollection
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->amastyOrderStatusCollection = $amastyOrderStatusCollection;
    }

    /**
     * @param array $statuses
     * @param string $stateToGetFor
     * @param bool $addLabels
     * @return array
     * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     */
    public function processStateStatuses($statuses, $stateToGetFor, $addLabels = true)
    {
        $statusCollection = $this->amastyOrderStatusCollection->create();
        if ($statusCollection->getSize() > 0) {
            $hideState = $this->scopeConfig->getValue('amostatus/general/hide_state');

            if (!is_array($stateToGetFor)) {
                $stateToGetFor = [$stateToGetFor];
            }

            foreach ($stateToGetFor as $getFor) {
                foreach ($statusCollection->getStates() as $state) {
                    if ($getFor == $state['value']) {
                        foreach ($statusCollection as $status) {
                            if ($status->getData('is_active') && !$status->getData('is_system')) {
                                // checking if we should apply status to the current state
                                $parentStates = [];

                                if ($status->getParentState()) {
                                    $parentStates = explode(',', $status->getParentState());
                                }

                                if (!$parentStates || in_array($state['value'], $parentStates)) {
                                    $elementName = $state['value'] . '_' . $status->getAlias();

                                    if ($addLabels) {
                                        $statuses[$elementName] = ($hideState ? '' : $state['label'] . ': ')
                                            . __($status->getStatus());
                                    } else {
                                        $statuses[] = $elementName;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $statuses;
    }

    /**
     * @param string $statusLabel
     * @param string $code
     * @return string
     * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     */
    public function processStatusLabel($statusLabel, $code)
    {
        if (empty($statusLabel) || (is_object($statusLabel) && !$statusLabel->getText())) {
            $statusCollection = $this->amastyOrderStatusCollection->create();

            if ($statusCollection->getSize() > 0) {
                $hideState = $this->scopeConfig->getValue('amostatus/general/hide_state');

                foreach ($statusCollection->getStates() as $state) {
                    foreach ($statusCollection as $status) {
                        if ($status->getData('is_active') && !$status->getData('is_system')) {
                            // checking if we should apply status to the current state
                            $parentStates = [];

                            if ($status->getParentState()) {
                                $parentStates = explode(',', $status->getParentState());
                            }

                            if (!$parentStates || in_array($state['value'], $parentStates)) {
                                $elementName = $state['value'] . '_' . $status->getAlias();

                                if ($code == $elementName) {
                                    $statusLabel = ($hideState ? '' : $state['label'] . ': ')
                                        . __($status->getStatus());

                                    break(2);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $statusLabel;
    }

    public function getStateStatuses(): array
    {
        $statusCollection = $this->amastyOrderStatusCollection->create();

        return $statusCollection->getAllStateStatuses();
    }

    public function getStatuses(): array
    {
        $statuses = [];
        $statusCollection = $this->amastyOrderStatusCollection->create();

        if ($statusCollection->getSize()) {
            $hideState = $this->scopeConfig->getValue('amostatus/general/hide_state');

            foreach ($statusCollection->getStates() as $state) {
                foreach ($statusCollection as $status) {
                    if ($status->getIsActive() && !$status->getIsSystem()) {
                        $parentStates = [];

                        if ($status->getParentState()) {
                            $parentStates = explode(',', $status->getParentState());
                        }

                        if (!$parentStates || in_array($state['value'], $parentStates)) {
                            $key = $state['value'] . '_' . $status->getAlias();
                            $statuses[$key] = ($hideState ? '' : $state['label'] . ': ') . __($status->getStatus());
                        }
                    }
                }
            }
        }

        return $statuses;
    }
}
