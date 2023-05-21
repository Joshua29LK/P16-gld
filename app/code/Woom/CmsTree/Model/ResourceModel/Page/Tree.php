<?php

namespace Woom\CmsTree\Model\ResourceModel\Page;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;
use Woom\CmsTree\Model\ResourceModel\Page\Tree\CollectionFactory;
use Woom\CmsTree\Api\Data\TreeInterface;
use Woom\CmsTree\Model\ResourceModel\Page\Tree\Collection;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Exception\LocalizedException;

class Tree extends AbstractDb
{
    /**
     * IF field name
     *
     * @var string
     */
    protected $_idFieldName = TreeInterface::TREE_ID;

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(TreeInterface::TREE_CMS_PAGE_TABLE, TreeInterface::TREE_ID);
    }

    /**
     * Tree collection factory
     *
     * @var CollectionFactory
     */
    protected $treeCollection;

    /**
     * Aggregate count
     *
     * @var AggregateCount
     */
    protected $aggregateCount;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Tree constructor.
     *
     * @param Context               $context
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory     $treeCollection
     * @param null                  $connectionName
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        CollectionFactory $treeCollection,
        $connectionName = null
    ) {
        $this->treeCollection = $treeCollection;
        $this->storeManager = $storeManager;
        parent::__construct($context, $connectionName);
    }

    /**
     * Get aggregate count object
     *
     * @return AggregateCount
     */
    private function getAggregateCount()
    {
        if (null === $this->aggregateCount) {
            $this->aggregateCount = \Magento\Framework\App\ObjectManager::getInstance()->get(AggregateCount::class);
        }

        return $this->aggregateCount;
    }

    /**
     * Process category data before delete
     * update children count for parent category
     * delete child categories
     *
     * @param AbstractModel $object
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _beforeDelete(AbstractModel $object)
    {
        parent::_beforeDelete($object);
        $this->getAggregateCount()->processDelete($object);
        $this->deleteChildren($object);

        return $this;
    }

    /**
     * Delete children categories of specific category
     *
     * @param AbstractModel $object
     *
     * @return $this
     */
    public function deleteChildren(AbstractModel $object)
    {
        if ($object->getSkipDeleteChildren()) {
            return $this;
        }

        $trees = $this->treeCollection->create();
        $trees->addFieldToFilter(TreeInterface::PATH, ['like' => $object->getPath() . '/%']);
        $childrenIds = $trees->getAllIds();
        foreach ($trees as $tree) {
            $tree->setSkipDeleteChildren(true);
            $tree->delete();
        }

        /**
         * Add deleted children ids to object
         * This data can be used in after delete event
         */
        $object->setDeletedChildrenIds($childrenIds);

        return $this;
    }

    /**
     * @param AbstractModel $object
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        parent::_beforeSave($object);

        if (!$object->getChildrenCount()) {
            $object->setChildrenCount(0);
        }

        if ($object->isObjectNew()) {
            if ($object->getPosition() === null) {
                $object->setPosition($object->getId());
            }
            $path = explode('/', $object->getPath());
            $level = count($path) - ($object->getId() ? 1 : 0);
            $toUpdateChild = array_diff($path, [$object->getId()]);

            if (!$object->hasPosition()) {
                $object->setPosition($object->getId());
            }
            if (!$object->hasLevel()) {
                $object->setLevel($level);
            }
            if ($object->getParentId() !== 0 && $level) {
                $object->setParentId($path[$level - 1]);
            }

            //if new object and parent id is not zero (root tree)
            if (!$object->getId() && $object->getParentId() !== 0) {
                $object->setPath($object->getPath() . '/');
            }

            $this->generateRequestUrl($object, $object->getPath());

            $this->getConnection()->update(
                $this->getMainTable(),
                [TreeInterface::CHILDREN_COUNT => new \Zend_Db_Expr(TreeInterface::CHILDREN_COUNT . '+1')],
                [TreeInterface::TREE_ID . ' IN(?)' => $toUpdateChild]
            );
        } else {
            $this->generateRequestUrl($object, $object->getPath());
        }

        return $this;
    }

    /**
     * After save actions
     *
     * @param AbstractModel $object
     *
     * @return AbstractDb
     * @throws LocalizedException
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->savePath($object);

        return parent::_afterSave($object);
    }

    /**
     * Update path field
     *
     * @param TreeInterface $object
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function savePath($object)
    {
        if ($object->getId() && substr($object->getPath(), -1) == '/') {
            $object->setPath($object->getPath() . $object->getId());

            $this->getConnection()->update(
                $this->getMainTable(),
                [TreeInterface::PATH => $object->getPath()],
                [TreeInterface::TREE_ID . ' = ?' => $object->getId()]
            );
            $object->unsetData('path_ids');
        }

        return $this;
    }

    /**
     * Generate request url for tree
     *
     * @param TreeInterface $object
     * @param array $path
     *
     * @return string|null
     * @throws LocalizedException
     */
    protected function generateRequestUrl($object, $path)
    {
        //update request url
        $pathIds = explode('/', $path);
        if ($object->getIdentifier() && $pathIds) {
            $currentPathId = array_search($object->getId(), $pathIds);
            if ($currentPathId) {
                unset($pathIds[$currentPathId]);
            }
            $select = $this->getConnection()->select()->from(
                $this->getMainTable(),
                [TreeInterface::TREE_ID, TreeInterface::IDENTIFIER]
            )->where(
                TreeInterface::TREE_ID . ' IN(?)',
                $pathIds
            )->where(
                TreeInterface::REQUEST_URL . ' IS NOT NULL'
            );

            $treeMap = $this->getConnection()->fetchPairs($select);

            $requestUrl = [];
            foreach ($pathIds as $pathId) {
                if (isset($treeMap[$pathId])) {
                    $requestUrl[] = $treeMap[$pathId];
                }
            }
            $requestUrl[] = $object->getIdentifier();
            $object->setRequestUrl(implode('/', $requestUrl));
        }

        return $object->getRequestUrl();
    }

    /**
     * Get children tree count
     *
     * @param int $treeId
     *
     * @return int
     * @throws LocalizedException
     */
    public function getChildrenCount($treeId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getMainTable(),
            TreeInterface::CHILDREN_COUNT
        )->where(
            TreeInterface::TREE_ID . ' = :' . TreeInterface::TREE_ID
        );
        $bind = [TreeInterface::TREE_ID => $treeId];

        return $this->getConnection()->fetchOne($select, $bind);
    }

    /**
     * Change page parent tree
     *
     * @param TreeInterface $tree
     * @param TreeInterface $newParent
     * @param null          $afterTreeId
     *
     * @return $this
     * @throws LocalizedException
     */
    public function changeParent(
        TreeInterface $tree,
        TreeInterface $newParent,
        $afterTreeId = null
    ) {
        $childrenCount = $this->getChildrenCount($tree->getId()) + 1;
        $table = $this->getMainTable();
        $connection = $this->getConnection();
        $levelFiled = $connection->quoteIdentifier(TreeInterface::LEVEL);
        $pathField = $connection->quoteIdentifier(TreeInterface::PATH);

        $connection->update(
            $table,
            [
                TreeInterface::CHILDREN_COUNT => new \Zend_Db_Expr(
                    TreeInterface::CHILDREN_COUNT . ' - ' . $childrenCount
                )
            ],
            [TreeInterface::TREE_ID . ' IN(?)' => $tree->getParentIds()]
        );

        $connection->update(
            $table,
            [
                TreeInterface::CHILDREN_COUNT => new \Zend_Db_Expr(
                    TreeInterface::CHILDREN_COUNT . ' + ' . $childrenCount
                )
            ],
            [TreeInterface::TREE_ID . ' IN(?)' => $newParent->getPathIds()]
        );

        $position = $this->processPositions($tree, $newParent, $afterTreeId);

        $newPath = sprintf('%s/%s', $newParent->getPath(), $tree->getId());
        $newLevel = $newParent->getLevel() + 1;
        $levelDisposition = $newLevel - $tree->getLevel();

        $connection->update(
            $table,
            [
                TreeInterface::PATH  => new \Zend_Db_Expr(
                    'REPLACE(' . $pathField . ',' . $connection->quote(
                        $tree->getPath() . '/'
                    ) . ', ' . $connection->quote(
                        $newPath . '/'
                    ) . ')'
                ),
                TreeInterface::LEVEL => new \Zend_Db_Expr($levelFiled . ' + ' . $levelDisposition)
            ],
            [$pathField . ' LIKE ?' => $tree->getPath() . '/%']
        );

        //generate request url by path
        $this->generateRequestUrl($tree, $newPath);

        $data = [
            TreeInterface::PATH           => $newPath,
            TreeInterface::LEVEL          => $newLevel,
            TreeInterface::POSITION       => $position,
            TreeInterface::PARENT_TREE_ID => $newParent->getId(),
            TreeInterface::REQUEST_URL    => $tree->getRequestUrl(),
        ];
        $connection->update($table, $data, [TreeInterface::TREE_ID . ' = ?' => $tree->getId()]);

        // Update tree object with new data
        $tree->addData($data);
        $tree->unsetData('path_ids');

        return $this;
    }

    /**
     * Process positions of old parent tree children and new parent tree children.
     * Get position for moved category
     *
     * @param TreeInterface $tree
     * @param TreeInterface $newParent
     * @param null|int      $afterTreeId
     *
     * @return int
     * @throws LocalizedException
     */
    protected function processPositions($tree, $newParent, $afterTreeId)
    {
        $table = $this->getMainTable();
        $connection = $this->getConnection();
        $positionField = $connection->quoteIdentifier(TreeInterface::POSITION);

        $bind = [TreeInterface::POSITION => new \Zend_Db_Expr($positionField . ' - 1')];
        $where = [
            TreeInterface::PARENT_TREE_ID . ' = ?' => $tree->getParentId(),
            $positionField . ' > ?'                => $tree->getPosition(),
        ];
        $connection->update($table, $bind, $where);

        /**
         * Prepare position value
         */
        if ($afterTreeId) {
            $select = $connection->select()->from($table, TreeInterface::POSITION)->where(
                TreeInterface::TREE_ID . ' = :' . TreeInterface::TREE_ID
            );
            $position = $connection->fetchOne($select, [TreeInterface::TREE_ID => $afterTreeId]);
            $position += 1;
        } else {
            $position = 1;
        }

        $bind = [TreeInterface::POSITION => new \Zend_Db_Expr($positionField . ' + 1')];
        $where = [TreeInterface::PARENT_TREE_ID . ' = ?' => $newParent->getId(), $positionField . ' >= ?' => $position];
        $connection->update($table, $bind, $where);

        return $position;
    }

    /**
     * Get first child for provided tree
     *
     * @param TreeInterface $object
     * @param int           $parentTreeId
     *
     * @return $this
     * @throws LocalizedException
     */
    public function getFirstChild($object, $parentTreeId)
    {
        $connection = $this->getConnection();
        if ($parentTreeId !== null) {
            $select = $this->getConnection()->select()->from(
                $this->getMainTable()
            )->where(
                TreeInterface::PATH . ' LIKE ? ',
                '%/' . $parentTreeId . '/%'
            )->orWhere(
                TreeInterface::PATH . '= ?',
                '%/' . $parentTreeId . '/%'
            )->where(
                TreeInterface::LEVEL . '= ?',
                2
            )->order(
                [TreeInterface::LEVEL, TreeInterface::POSITION]
            )->limit(
                1
            );

            $data = $connection->fetchRow($select);

            if ($data) {
                $object->setData($data);
            }
        }

        $this->_afterLoad($object);

        return $this;
    }

    /**
     * Get direct children trees for this tree
     *
     * @param TreeInterface $object
     *
     * @return Collection
     * @throws \Exception
     */
    public function getDirectChildren($object)
    {
        /** @var Collection $trees */
        $trees = $this->treeCollection->create();
        $trees->addFieldToFilter(TreeInterface::PATH, ['like' => $object->getPath() . '/%']);
        $trees->addFieldToFilter(TreeInterface::LEVEL, $object->getLevel() + 1);
        $trees->setOrder(TreeInterface::LEVEL, Collection::SORT_ORDER_ASC);
        $trees->setOrder(TreeInterface::POSITION, Collection::SORT_ORDER_ASC);

        $trees->addCmsActiveColumn();

        return $trees;
    }

    /**
     * Get child page ids for this tree
     *
     * @param TreeInterface $object
     *
     * @return array
     * @throws \Exception
     */
    public function getChildPageIds($object)
    {
        /** @var Collection $trees */
        $trees = $this->treeCollection->create();
        $trees->addFieldToFilter(TreeInterface::PATH, ['like' => $object->getPath() . '/%']);
        $pageIds = $trees->getColumnValues('page_id');

        return $pageIds;
    }

    /**
     * Get tree by request URL
     *
     * @param Tree   $object
     * @param string $url
     * @param int    $storeId
     *
     * @return $this
     * @throws LocalizedException
     */
    public function getByRequestUrl($object, $url, $storeId)
    {
        $connection = $this->getConnection();
        if ($url !== null) {
            $select = $this->_getLoadSelect(TreeInterface::REQUEST_URL, $url, $object);

            $bind = [
                'is_active_value' => 1,
                'store_id'        => $storeId,
                'zero_store_id'   => 0
            ];
            if ($object) {
                $pageLinkField = TreeInterface::PAGE_ID;
                $pageTable = $this->getTable(
                    TreeInterface::CMS_PAGE_TABLE
                );

                $linkField = TreeInterface::PAGE_ID;
                $storeTable = $this->getTable(
                    TreeInterface::CMS_PAGE_STORE_TABLE
                );

                $select = $select->joinInner(
                    ['p' => $pageTable],
                    "{$this->getMainTable()}.{$pageLinkField} = p.{$pageLinkField}",
                    ['page_identifier' => 'identifier']
                );

                $select->joinInner(
                    ['s' => $storeTable],
                    "{$this->getMainTable()}.{$linkField} = s.{$linkField}",
                    []
                )->where(
                    's.store_id = :store_id OR s.store_id = :zero_store_id'
                )->where(
                    'p.' . PageInterface::IS_ACTIVE . ' = :is_active_value'
                );
                $select->getConnection()->fetchCol(
                    $select,
                    $bind
                );
            }
            $data = $connection->fetchRow($select, $bind);

            if ($data) {
                $object->setData($data);
            }
        }

        $this->_afterLoad($object);

        return $this;
    }
}
