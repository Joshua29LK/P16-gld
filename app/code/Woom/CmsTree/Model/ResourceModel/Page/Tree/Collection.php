<?php

namespace Woom\CmsTree\Model\ResourceModel\Page\Tree;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\DB\Helper;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Store\Model\Store;
use Woom\CmsTree\Api\Data\TreeInterface;

class Collection extends AbstractCollection
{
    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * TreeCollection constructor.
     *
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface        $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface       $eventManager
     * @param MetadataPool           $metadataPool
     * @param Helper                 $helper
     * @param AdapterInterface|null  $connection
     * @param AbstractDb|null        $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        MetadataPool $metadataPool,
        Helper $helper,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->helper = $helper;
        $this->metadataPool = $metadataPool;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Woom\CmsTree\Model\Page\Tree', 'Woom\CmsTree\Model\ResourceModel\Page\Tree');
    }

    /**
     * Add Cms Page table to collection
     *
     * @return $this
     * @throws \Exception
     */
    public function addCmsPages()
    {
        $entityMetadata = $this->metadataPool->getMetadata(PageInterface::class);

        if (!$this->getFlag('cms_page_data_joined')) {
            $this->getSelect()->joinLeft(
                ['page_table' => $entityMetadata->getEntityTable()],
                'main_table.page_id = page_table.'.$entityMetadata->getIdentifierField(),
                ['page_title' => 'title', 'page_identifier' => 'identifier']
            );
            $this->setFlag('cms_page_data_joined', true);
        }

        return $this;
    }

    /**
     * Add Cms Page active column table to collection
     *
     * @return $this
     * @throws \Exception
     */
    public function addCmsActiveColumn()
    {
        $entityMetadata = $this->metadataPool->getMetadata(PageInterface::class);

        if (!$this->getFlag('cms_page_active_data_joined')) {
            $this->getSelect()->joinLeft(
                ['page_table' => $entityMetadata->getEntityTable()],
                'main_table.page_id = page_table.'.$entityMetadata->getIdentifierField(),
                ['is_active' => 'is_active']
            );
            $this->setFlag('cms_page_active_data_joined', true);
        }

        return $this;
    }

    /**
     * Add store filter to tree collection
     *
     * @param string|Store $store
     * @param bool         $withAdmin
     *
     * @return $this
     * @throws \Exception
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof Store) {
            $store = $store->getId();
        }

        if ($withAdmin) {
            $storeIds = [Store::DEFAULT_STORE_ID, $store];
        } else {
            $storeIds = [$store];
        }

        $this->addStoresColumn();
        $this->getSelect()->having(
            'main_table.page_id IS NULL OR (page_in_stores IS NOT NULL OR page_in_stores IN (?))',
            $storeIds
        );

        return $this;
    }

    /**
     * Add menu filter to tree collection
     *
     * @param string $value
     *
     * @return $this
     */
    public function addMenuFilter($value)
    {
        $this->getSelect()->where(
            'main_table.is_in_menu = ?',
            $value
        );

        return $this;
    }

    /**
     * Add stores column to tree collection
     *
     * @return $this
     * @throws \Exception
     */
    public function addStoresColumn()
    {
        $entityMetadata = $this->metadataPool->getMetadata(PageInterface::class);
        $linkField = $entityMetadata->getLinkField();

        if (!$this->getFlag('stores_data_joined')) {
            $subSelect = $this->getConnection()->select();
            $subSelect->from(
                ['store' => $this->getTable('cms_page_store')],
                []
            )->where(
                'store.'.$linkField.' = main_table.page_id'
            );
            $subSelect = $this->helper->addGroupConcatColumn($subSelect, 'store_id', 'store_id');
            $this->getSelect()->columns(['page_in_stores' => new \Zend_Db_Expr('('.$subSelect.')')]);

            $this->setFlag('stores_data_joined', true);
        }

        return $this;
    }

    /**
     * Add Id filter
     *
     * @param array $treeIds
     *
     * @return $this
     */
    public function addIdFilter($treeIds)
    {
        if (is_array($treeIds)) {
            if (empty($treeIds)) {
                $condition = '';
            } else {
                $condition = ['in' => $treeIds];
            }
        } elseif (is_numeric($treeIds)) {
            $condition = $treeIds;
        } elseif (is_string($treeIds)) {
            $ids = explode(',', $treeIds);
            if (empty($ids)) {
                $condition = $treeIds;
            } else {
                $condition = ['in' => $ids];
            }
        }
        $this->addFieldToFilter(TreeInterface::TREE_ID, $condition);

        return $this;
    }
}
