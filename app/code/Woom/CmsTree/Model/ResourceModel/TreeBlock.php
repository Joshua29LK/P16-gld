<?php

namespace Woom\CmsTree\Model\ResourceModel;

use Magento\Cms\Model\ResourceModel\Page;
use Magento\Framework\App\CacheInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\ManagerInterface;
use Woom\CmsTree\Model\ResourceModel\Page\Tree\CollectionFactory;
use Woom\CmsTree\Model\ResourceModel\Page\Tree\Collection;
use Woom\CmsTree\Model\Page\Tree as TreeModel;
use Woom\CmsTree\Api\Data\TreeInterface;
use Magento\Cms\Model\Page as PageModel;

class TreeBlock extends AbstractTreeBlock
{
    /**
     * Entity field name
     *
     * @var string
     */
    protected $entityFieldName = 'tree_id';

    /**
     * Entity table name
     *
     * @var string
     */
    protected $entityTableName = TreeInterface::TREE_CMS_PAGE_TABLE;

    /**
     * Id field
     *
     * @var string
     */
    protected $idField = TreeInterface::TREE_ID;

    /**
     * Path field
     *
     * @var string
     */
    protected $pathField = TreeInterface::PATH;

    /**
     * Order field
     *
     * @var string
     */
    protected $orderField = TreeInterface::POSITION;

    /**
     * Level field
     *
     * @var string
     */
    protected $levelField = TreeInterface::LEVEL;

    /**
     * Cache tags to clear
     *
     * @var array
     */
    protected $clearCacheTags = [PageModel::CACHE_TAG];

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
        parent::__construct(
            $cmsPage,
            $cache,
            $storeManager,
            $resource,
            $eventManager,
            $collectionFactory,
            $treeModel
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addCollectionFilters($collection)
    {
        $collection->addCmsActiveColumn();

        return $this;
    }

    /**
     * Get real existing page ids by specified ids
     *
     * @param array $ids
     *
     * @return array
     */
    public function getExistingPageIdsBySpecifiedIds($ids)
    {
        if (empty($ids)) {
            return [];
        }
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $select = $this->_conn->select()->from($this->_table, ['page_id'])->where('page_id IN (?)', $ids);

        return $this->_conn->fetchCol($select);
    }
}
