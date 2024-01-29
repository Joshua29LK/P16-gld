<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Status for Magento 2
 */

namespace Amasty\OrderStatus\Model\ResourceModel\Status;

use Amasty\OrderStatus\Model\ResourceModel\Status as StatusResource;
use Amasty\OrderStatus\Model\Status as StatusModel;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Model\Order\Config as OrderConfig;
use Psr\Log\LoggerInterface;

class Collection extends AbstractCollection
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var OrderConfig
     */
    private $orderConfig;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StatusResource $resource = null,
        ScopeConfigInterface $scopeConfig,
        OrderConfig $orderConfig
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, null, $resource);

        $this->scopeConfig = $scopeConfig;
        $this->orderConfig = $orderConfig;
    }

    protected function _construct()
    {
        $this->_init(StatusModel::class, StatusResource::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    public function toOptionArray()
    {
        $statuses = $this->orderConfig->getStatuses();

        if ($this->getSize() > 0) {
            $hideState = $this->scopeConfig->getValue('amostatus/general/hide_state');

            foreach ($this->getStates() as $state) {
                foreach ($this as $status) {
                    if ($status->getData('is_active') && !$status->getData('is_system')) {
                        // checking if we should apply status to the current state
                        $parentStates = [];

                        if ($status->getParentState()) {
                            $parentStates = explode(',', $status->getParentState());
                        }

                        if (!$parentStates || in_array($state['value'], $parentStates)) {
                            $elementName = $state['value'] . '_' . $status->getAlias();
                            $statuses[$elementName] = ($hideState ? '' : $state['label'] . ': ')
                                . __($status->getStatus());
                        }
                    }
                }
            }
        }

        foreach ($statuses as $value => $label) {
            $statuses[] = ['value' => $value, 'label' => $label];
            unset($statuses[$value]);
        }

        return $statuses;
    }

    public function getStates()
    {
        $states = $this->orderConfig->getStates();

        foreach ($states as $key => $value) {
            $states[] = ['value' => $key, 'label' => __(ucwords(str_replace('_', ' ', $key)))];
            unset($states[$key]);
        }

        return $states;
    }

    public function getAllStateStatuses()
    {
        $allStateStatuses = [];

        foreach ($this as $status) {
            $alias = $status->getAlias();
            $parentStates = explode(',', $status->getParentState());

            foreach ($parentStates as $state) {
                $allStateStatuses[] = $state . '_' . $alias;
            }
        }

        return $allStateStatuses;
    }
}
