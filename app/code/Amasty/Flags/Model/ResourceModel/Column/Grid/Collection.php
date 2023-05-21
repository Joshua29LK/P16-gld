<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

namespace Amasty\Flags\Model\ResourceModel\Column\Grid;

use Amasty\Flags\Model\Flag;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Amasty\Flags\Model\ResourceModel\Column\Collection as ColumnCollection;

class Collection extends ColumnCollection implements SearchResultInterface
{
    /**
     * @var AggregationInterface
     */
    private $aggregations;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    private $orderConfig;
    /**
     * @var \Magento\Shipping\Model\Config\Source\Allmethods
     */
    private $shippingConfig;
    /**
     * @var \Amasty\Flags\Model\ResourceModel\Flag\CollectionFactory
     */
    private $flagCollectionFactory;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        \Amasty\Flags\Model\ResourceModel\Flag\CollectionFactory $flagCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Shipping\Model\Config\Source\Allmethods $shippingConfig,
        $model = \Magento\Framework\View\Element\UiComponent\DataProvider\Document::class,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
        $this->scopeConfig = $scopeConfig;
        $this->orderConfig = $orderConfig;
        $this->shippingConfig = $shippingConfig;
        $this->flagCollectionFactory = $flagCollectionFactory;
    }

    /**
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * Retrieve all ids for collection
     * Backward compatibility with EAV collection
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    protected function _beforeLoad()
    {
        $innerSelect = $this->getConnection()->select()
            ->from(
                ['fc' => $this->getTable('amasty_flags_flag_column')],
                new \Zend_Db_Expr('GROUP_CONCAT(fc.flag_id)')
            )
            ->where('fc.column_id = main_table.id')
        ;

        $this->getSelect()->columns([
            'applied_flags' => $innerSelect
        ]);

        return parent::_beforeLoad();
    }

    protected function _afterLoad()
    {
        /** @var \Amasty\Flags\Model\ResourceModel\Flag\Collection $flags */
        $flags = $this->flagCollectionFactory->create();

        foreach ($this as $item) {
            if ($item->getAppliedFlags()) {
                $flagsHtml = [];

                $flagIds = explode(',', $item->getAppliedFlags());

                foreach ($flagIds as $flagId) {
                    /** @var Flag $flag */
                    $flag = $flags->getItemById($flagId);
                    if ($flag) {
                        $flagsHtml []= "<img src=\"{$flag->getImageUrl()}\" /> {$flag->getName()}";
                    }
                }

                $item->setData('applied_flags', implode(', ', $flagsHtml));
            }
        }

        return parent::_afterLoad();
    }
}
