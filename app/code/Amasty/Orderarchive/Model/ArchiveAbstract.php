<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Archive for Magento 2
*/

namespace Amasty\Orderarchive\Model;

use Amasty\Orderarchive\Helper\Data;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

abstract class ArchiveAbstract extends AbstractDb
{
    public const ARCHIVE_ENTITY_ID = 'entity_id';
    public const BATCH_SIZE = 100;

    /**
     * @var string
     */
    public $baseTable;

    /**
     * @var string
     */
    protected $archiveTable;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Amasty\Orderarchive\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var bool
     */
    protected $configMassfilter = false;

    /**
     * @var bool
     */
    protected $configDayAgo = false;

    /**
     * @var bool
     */
    protected $configStatus = false;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var array
     */
    protected $availableRemoveCollections = ['invoice', 'shipments', 'creditmemos'];

    /**
     * @var TableStructureSynchronizer
     */
    private $tableStructureSynchronizer;

    /**
     * ArchiveAbstract constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Amasty\Orderarchive\Helper\Data $helper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Amasty\Orderarchive\Helper\Data $helper,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        TableStructureSynchronizer $tableStructureSynchronizer,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->connection = $this->getConnection();
        $this->helper = $helper;
        $this->dateTime = $dateTime;
        $this->initArchiveConfig();
        $this->quoteRepository = $quoteRepository;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->tableStructureSynchronizer = $tableStructureSynchronizer;
    }

    /**
     *
     * Move data by params from one table in other one
     * @param string $tableFrom
     * @param string $tableTo
     * @param array &$params
     * @return array Array displaced Orders
     */
    protected function move($tableFrom, $tableTo, array &$params)
    {
        $params = $this->prepare($tableFrom, $tableTo, $params);

        $tableFromKeys = array_keys($this->connection->describeTable($params['tableFrom']));
        $tableToKeys = array_keys($this->connection->describeTable($params['tableTo']));

        $insertFields = array_intersect(
            $tableFromKeys,
            $tableToKeys
        );
        $select = $this->getSelect($params['tableFrom'], $params['archive_ids']);

        if (count($tableFromKeys) !== count($tableToKeys)) {
            $select->reset(Select::COLUMNS)
                ->columns($insertFields);
        }

        $idsSelect = clone $select;
        $idsSelect->reset(Select::COLUMNS)->columns('increment_id');

        $movedIds = $this->connection->fetchCol($idsSelect);

        if ($movedIds) {
            $this->connection->exec($select->insertFromSelect($params['tableTo'], $insertFields, true));
            $this->removeFromGrid($params['tableFrom'], $params['archive_ids']);
        }

        return $movedIds;
    }

    /**
     * @param string|object $table
     * @return string
     */
    protected function prepareTable($table)
    {
        if (is_object($table) && !is_string($table)) {
            return $this->getMainTable();
        }

        return $this->getTable($table);
    }

    /**
     * @param $params
     * @return array
     */
    protected function prepareParams($params)
    {
        return [self::ARCHIVE_ENTITY_ID => $params];
    }

    /**
     * @param $tableName
     * @param array $params
     * @return mixed
     */
    protected function removeFromGrid($tableName, array $params)
    {
        $select = $this->getSelect($this->getTable($tableName), $params);

        return $this->connection->exec($select->deleteFromSelect($this->getTable($tableName)));
    }

    /**
     * @param $tableFrom
     * @param $tableTo
     * @param $params
     * @return array
     */
    protected function prepare($tableFrom, $tableTo, $params)
    {
        $this->baseTable = $this->prepareTable($tableFrom);
        $this->archiveTable = $this->prepareTable($tableTo);

        return [
            'archive_ids' => $this->prepareParams($params),
            'tableFrom' => $this->baseTable,
            'tableTo' => $this->archiveTable
        ];
    }

    /**
     * @param $model
     * @param $archiveIds
     * @return array
     */
    public function addToArchive($model, $archiveIds)
    {
        if (array_key_exists('source_table', $model) && array_key_exists('target_table', $model)) {
            $sourceTable = $model['source_table'];
            $targetTable = $model['target_table'];

            $this->tableStructureSynchronizer->execute(
                $this->prepareTable($sourceTable),
                $this->prepareTable($targetTable)
            );

            $batchPools = array_chunk($archiveIds, self::BATCH_SIZE);
            $finishedIds = [];
            foreach ($batchPools as $batchPool) {
                $finishedIds[] = $this->move($sourceTable, $targetTable, $batchPool);
            }
            return empty($finishedIds) ? [] : array_merge(...$finishedIds);
        }

        return [];
    }

    /**
     * @param $model
     * @param $archiveIds
     * @return array
     */
    public function removeFromArchive($model, $archiveIds)
    {
        if (array_key_exists('source_table', $model) && array_key_exists('target_table', $model)) {
            $sourceTable = $model['source_table'];
            $targetTable = $model['target_table'];

            $this->tableStructureSynchronizer->execute(
                $this->prepareTable($sourceTable),
                $this->prepareTable($targetTable),
                true
            );

            $batchPools = array_chunk($archiveIds, self::BATCH_SIZE);
            $finishedIds = [];
            foreach ($batchPools as $batchPool) {
                $finishedIds[] = $this->move($targetTable->_mainTable, $sourceTable, $batchPool);
            }
            return empty($finishedIds) ? [] : array_merge(...$finishedIds);
        }

        return [];
    }

    /**
     * @param $tableName
     * @param array $params
     * @return Select
     */
    protected function getSelect($tableName, array $params)
    {
        $select = $this->connection->select()->from($tableName);

        if (isset($params[self::ARCHIVE_ENTITY_ID])) {
            $select->where($this->getOrderIdCondition($params));
        }

        return $select;
    }

    protected function getOrderIdCondition(array $params): string
    {
        return $this->connection->quoteInto(' `order_increment_id` IN (?)', $params[self::ARCHIVE_ENTITY_ID]);
    }

    /**
     * @param $selectedIds
     * @return array|mixed
     */
    public function removePermanently($selectedIds)
    {
        $res = [];
        $params = $this->prepareParams($selectedIds);
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(key($params), current($params), 'in')->create();
        $orders = $this->orderRepository->getList($searchCriteria);

        if ($orders->getSize()) {
            foreach ($orders as $item) {

                try {
                    $quote = $this->quoteRepository->get($item->getQuoteId());
                    if ($quote->getId()) {
                        $this->quoteRepository->delete($quote);
                    }
                } catch (NoSuchEntityException $e) {
                    //skip if quote wil be deleted (for some Modules, which deleted expired quote by cron)
                    null;
                }

                $res['order'][] = $item->getId();
                $this->deleteAllForOrder($item, $res);
                $item->delete();
            }

            $this->removeFromGrid(ArchiveFactory::ORDER_ARCHIVE_NAMESPACE, $params);

            return $res;
        }

        return [];
    }

    /**
     * @param $order
     * @param $result
     * @return mixed
     */
    protected function deleteAllForOrder($order, &$result)
    {
        if ($order->getId()) {
            foreach ($this->availableRemoveCollections as $type) {
                $collectionName = 'get' . ucfirst($type) . 'Collection';
                $collection = $order->$collectionName();

                if ($collection->getSize()) {
                    foreach ($collection as $item) {
                        $result[$type][] = $item->getId();
                        $item->delete();
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @return void
     */
    protected function initArchiveConfig()
    {
        $this->configDayAgo =
            $this->helper->getConfigValueByPath(Data::CONFIG_PATH_GENERAL_DAY_AGO);
        $this->configMassfilter =
            $this->helper->getConfigValueByPath(Data::CONFIG_PATH_GENERAL_ENABLE_MASSFILTER);
        $this->configStatus =
            $this->helper->getConfigValueByPath(Data::CONFIG_PATH_GENERAL_STATUS);
    }
}
