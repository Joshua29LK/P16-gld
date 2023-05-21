<?php

namespace Woom\CmsTree\Model\ResourceModel;

use Magento\Framework\Data\Tree\Dbp;
use Magento\Cms\Model\ResourceModel\Page;
use Magento\Framework\App\CacheInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\ManagerInterface;
use Woom\CmsTree\Model\ResourceModel\Page\Tree\CollectionFactory;
use Woom\CmsTree\Model\ResourceModel\Page\Tree\Collection;
use Woom\CmsTree\Model\Page\Tree as TreeModel;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Exception\NoSuchEntityException;

class AbstractTreeBlock extends Dbp
{
    /**
     * Entity field name
     *
     * @var string
     */
    protected $entityFieldName = 'entity_id';

    /**
     * Entity table name
     *
     * @var string
     */
    protected $entityTableName = '';

    /**
     * Id field
     *
     * @var string
     */
    protected $idField = 'id';

    /**
     * Path field
     *
     * @var string
     */
    protected $pathField = 'path';

    /**
     * Order field
     *
     * @var string
     */
    protected $orderField = 'order';

    /**
     * Level field
     *
     * @var string
     */
    protected $levelField = 'level';

    /**
     * Cache tags to clear
     *
     * @var array
     */
    protected $clearCacheTags = [];

    /**
     * Event manager
     *
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * Tree collection factory
     *
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * Tree model
     *
     * @var TreeModel
     */
    private $treeModel;

    /**
     * Tree collection
     *
     * @var Collection
     */
    protected $collection;

    /**
     * Inactive page ids
     *
     * @var null
     */
    protected $inactivePageIds = null;

    /**
     * Store id
     *
     * @var null
     */
    protected $storeId = null;

    /**
     * Resource
     *
     * @var ResourceConnection
     */
    protected $coreResource;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Cache
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     * Cms page resource model
     *
     * @var Page
     */
    protected $cmsPage;

    /**
     * TreeBlock constructor.
     *
     * @param Page                  $cmsPage
     * @param CacheInterface        $cache
     * @param StoreManagerInterface $storeManager
     * @param ResourceConnection    $resource
     * @param ManagerInterface      $eventManager
     * @param CollectionFactory     $collectionFactory
     * @param TreeModel             $treeModel
     */
    public function __construct(
        Page $cmsPage,
        CacheInterface $cache,
        StoreManagerInterface $storeManager,
        ResourceConnection $resource,
        ManagerInterface $eventManager,
        CollectionFactory $collectionFactory,
        TreeModel $treeModel
    ) {
        $this->treeModel = $treeModel;
        $this->cmsPage = $cmsPage;
        $this->cache = $cache;
        $this->storeManager = $storeManager;
        $this->coreResource = $resource;
        parent::__construct(
            $resource->getConnection('write'),
            $resource->getTableName($this->entityTableName),
            [
                Dbp::ID_FIELD    => $this->idField,
                Dbp::PATH_FIELD  => $this->pathField,
                Dbp::ORDER_FIELD => $this->orderField,
                Dbp::LEVEL_FIELD => $this->levelField
            ]
        );
        $this->eventManager = $eventManager;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Set store id
     *
     * @param integer $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = (int)$storeId;

        return $this;
    }

    /**
     * Return store id
     *
     * @return integer
     * @throws NoSuchEntityException
     */
    public function getStoreId()
    {
        if ($this->storeId === null) {
            $this->storeId = $this->storeManager->getStore()->getId();
        }

        return $this->storeId;
    }

    /**
     * Add collection data to tree
     *
     * @param Collection $collection
     *
     * @return $this
     */
    public function addCollectionData($collection)
    {
        if ($collection == null) {
            $collection = $this->getCollection();
        } else {
            $this->setCollection($collection);
        }

        $nodeIds = [];
        foreach ($this->getNodes() as $node) {
            $nodeIds[] = $node->getId();
        }

        $collection->addIdFilter($nodeIds);
        $this->addCollectionFilters($collection);
        $collection->load();

        foreach ($collection as $treeRow) {
            if ($this->getNodeById($treeRow->getId())) {
                $this->getNodeById($treeRow->getId())->addData($treeRow->getData());
            }
        }

        foreach ($this->getNodes() as $node) {
            if (!$collection->getItemById($node->getId()) && $node->getParent()) {
                $this->removeNode($node);
            }
        }

        return $this;
    }

    /**
     * Get pages collection
     *
     * @return Collection
     */
    public function getCollection()
    {
        if ($this->collection === null) {
            $this->collection = $this->getDefaultCollection();
        }

        return $this->collection;
    }

    /**
     * Clean object cache recursively
     *
     * @param Collection|array $object
     *
     * @return void
     */
    protected function clean($object)
    {
        if (is_array($object)) {
            foreach ($object as $obj) {
                $this->clean($obj);
            }
        }
        unset($object);
    }

    /**
     * Set collection
     *
     * @param Collection $collection
     *
     * @return $this
     */
    public function setCollection($collection)
    {
        if ($this->collection !== null) {
            $this->clean($this->collection);
        }
        $this->collection = $collection;

        return $this;
    }

    /**
     * Add collection filters before load
     *
     * @param Collection $collection
     *
     * @return $this
     */
    public function addCollectionFilters($collection)
    {
        return $this;
    }

    /**
     * Init default collection
     *
     * @return Collection
     */
    protected function getDefaultCollection()
    {
        $collection = $this->collectionFactory->create();

        return $collection;
    }

    /**
     * Move node to new parent and clear cache
     *
     * @param Node $node
     * @param Node $newParent
     * @param Node $prevNode
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function move($node, $newParent, $prevNode = null)
    {
        $this->treeModel->move($node->getId(), $newParent->getId());
        parent::move($node, $newParent, $prevNode);

        $this->afterMove();
    }

    /**
     * Move tree after
     *
     * @return $this
     */
    protected function afterMove()
    {
        $this->cache->clean($this->clearCacheTags);

        return $this;
    }

    /**
     * Load whole page tree, that will include specified pages ids.
     *
     * @param array $ids
     *
     * @return $this|bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function loadByIds($ids)
    {
        $levelField = $this->_conn->quoteIdentifier('level');
        $pathField = $this->_conn->quoteIdentifier('path');
        // load first two levels, if no ids specified
        if (empty($ids)) {
            $select = $this->_conn->select()->from($this->_table, $this->entityFieldName)->where(
                $levelField.' <= 2'
            );
            $ids = $this->_conn->fetchCol($select);
        }
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        foreach ($ids as $key => $id) {
            $ids[$key] = (int)$id;
        }

        // collect paths of specified IDs and prepare to collect all their parents and neighbours
        $select = $this->_conn->select()->from($this->_table, ['path', 'level'])->where(
            $this->entityFieldName.' IN (?)',
            $ids
        );
        $where = [$levelField.'=0' => true];

        foreach ($this->_conn->fetchAll($select) as $item) {
            $pathIds = explode('/', $item['path']);
            $level = (int)$item['level'];
            while ($level > 0) {
                $pathIds[count($pathIds) - 1] = '%';
                $path = implode('/', $pathIds);
                $where["{$levelField}={$level} AND {$pathField} LIKE '{$path}'"] = true;
                array_pop($pathIds);
                $level--;
            }
        }
        $where = array_keys($where);

        // get all required records
        $select = $this->getDefaultCollection();
        $select->where(implode(' OR ', $where));

        // get array of records and add them as nodes to the tree
        $arrNodes = $this->_conn->fetchAll($select);
        if (!$arrNodes) {
            return false;
        }
        $childrenItems = [];
        foreach ($arrNodes as $key => $nodeInfo) {
            $pathToParent = explode('/', $nodeInfo[$this->_pathField]);
            array_pop($pathToParent);
            $pathToParent = implode('/', $pathToParent);
            $childrenItems[$pathToParent][] = $nodeInfo;
        }
        $this->addChildNodes($childrenItems, '', null);

        return $this;
    }
}
