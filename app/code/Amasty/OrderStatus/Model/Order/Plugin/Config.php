<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Order Status for Magento 2
 */

namespace Amasty\OrderStatus\Model\Order\Plugin;

use Amasty\OrderStatus\Model\Order\ConfigProcessor;
use Magento\Framework\App\State;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory;

//phpcs:ignoreFile
class Config extends \Magento\Sales\Model\Order\Config
{
    /**
     * @var ConfigProcessor
     */
    private $configProcessor;

    public function __construct(
        StatusFactory $orderStatusFactory,
        CollectionFactory $orderStatusCollectionFactory,
        State $state,
        ConfigProcessor $configProcessor
    ) {
        $this->configProcessor = $configProcessor;
        parent::__construct($orderStatusFactory, $orderStatusCollectionFactory, $state);
    }

    public function aroundGetStateStatuses($subject, $proceed, $stateToGetFor, $addLabels = true)
    {
        $statuses = $proceed($stateToGetFor);
        $statuses = $this->configProcessor->processStateStatuses($statuses, $stateToGetFor, $addLabels);

        return $statuses;
    }

    public function aroundGetStatusLabel($subject, $proceed, $code)
    {
        $statusLabel = $proceed($code);

        return $this->configProcessor->processStatusLabel($statusLabel, $code);
    }

    public function afterGetVisibleOnFrontStatuses($subject, $result)
    {
        $statuses = $this->configProcessor->getStateStatuses();

        return array_merge($result, $statuses);
    }

    public function afterGetStatuses($subject, $result)
    {
        $statuses = $this->configProcessor->getStatuses();

        return array_merge($result, $statuses);
    }

    public function aroundGetStatusFrontendLabel($subject, $proceed, $code)
    {
        return $this->aroundGetStatusLabel($subject, $proceed, $code);
    }
}
