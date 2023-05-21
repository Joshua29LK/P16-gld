<?php

namespace Woom\CmsTree\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Woom\CmsTree\Api\Data\TreeInterface;
use Woom\CmsTree\Api\TreeRepositoryInterface;
use Woom\CmsTree\Model\Page\TreeFactory;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
use Woom\CmsTree\Model\Page\Tree;
use Magento\Store\Model\Store;
use Magento\Framework\Exception\LocalizedException;

class InstallData implements InstallDataInterface
{
    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * CMS page repository
     *
     * @var PageRepositoryInterface
     */
    protected $pageRepository;

    /**
     * CMS tree model repository
     *
     * @var TreeRepositoryInterface
     */
    protected $treeRepository;

    /**
     * CMS Tree model factory
     *
     * @var TreeFactory
     */
    protected $treeFactory;

    /**
     * Page collection factory
     *
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Array of tree id => path mappings
     *
     * @var array
     */
    protected $pathMap;

    /**
     * InstallData constructor.
     *
     * @param StoreManagerInterface   $storeManager
     * @param PageRepositoryInterface $pageRepository
     * @param TreeRepositoryInterface $treeRepository
     * @param TreeFactory             $treeFactory
     * @param CollectionFactory       $collectionFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        PageRepositoryInterface $pageRepository,
        TreeRepositoryInterface $treeRepository,
        TreeFactory $treeFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->storeManager = $storeManager;
        $this->pageRepository = $pageRepository;
        $this->treeRepository = $treeRepository;
        $this->treeFactory = $treeFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if ($setup->tableExists('woom_cms_page_tree')) {
            $select = $setup->getConnection()->select()->from(
                $setup->getTable('woom_cms_page_tree'),
                'COUNT(*)'
            );

            //if we already have CMS pages in tree, that means the script already ran
            $count = $setup->getConnection()->fetchOne($select);
            if ($count > 0) {
                return $this;
            }
        }

        $setup->startSetup();

        $this->setRootTreeIdsPerStore($setup);
        $this->initCmsPagesPerStore();

        $setup->endSetup();
    }

    /**
     * Add root tree and Create tree for each store
     *
     * @param ModuleDataSetupInterface $setup
     *
     * @return void
     * @throws LocalizedException
     */
    public function setRootTreeIdsPerStore($setup)
    {
        //create root tree
        /** @var Tree $rootTree */
        $rootTree = $this->treeFactory->create();
        $rootTree->setTitle('Root');
        $rootTree->setParentId(0);
        $rootTree->setPath(TreeInterface::TREE_ROOT_ID);
        $rootTree->setLevel(0);
        $rootTree->setMenuLabel(null);
        $rootTree->setIsIsMenu(false);
        $this->treeRepository->save($rootTree);

        //create root tree per store
        $stores = $this->storeManager->getStores(true);
        foreach ($stores as $store) {
            $title = $store->getName();
            if ($store->getCode() == Store::ADMIN_CODE) {
                $title = ('All Store Views');
            }
            /** @var Tree $tree */
            $tree = $this->treeFactory->create();
            $tree->setTitle($title);
            $tree->setParentId(TreeInterface::TREE_ROOT_ID);
            $tree->setPath(TreeInterface::TREE_ROOT_ID);
            $tree->setPosition($store->getId());
            $tree->setLevel(1);
            $tree->setMenuLabel(null);
            $tree->setIsIsMenu(false);
            $tree = $this->treeRepository->save($tree);

            $store->setRootCmsTreeId($tree->getId());

            //update via ORM, because store save triggers plugins we don't want to trigger
            $setup->getConnection()->update(
                $store->getResource()->getMainTable(),
                [TreeInterface::ROOT_CMS_TREE_ID_COLUMN => $tree->getId()],
                [$store->getResource()->getIdFieldName() . ' = ?' => (int)$store->getId()]
            );

            //save path mapping to use for cms tree paths
            $this->pathMap[$tree->getId()] = $tree->getPath();
        }
    }

    /**
     * Init cms pages for each store
     *
     * @return void
     * @throws LocalizedException
     */
    public function initCmsPagesPerStore()
    {
        $stores = $this->storeManager->getStores(true);
        foreach ($stores as $store) {
            $position = 0;
            $rootCmsTreeId = $store->getRootCmsTreeId();
            $path = $this->pathMap[$rootCmsTreeId];
            $pages = $this->collectionFactory->create()->addStoreFilter($store->getId(), false);
            foreach ($pages as $page) {
                /** @var Tree $tree */
                $tree = $this->treeFactory->create();
                $tree->setTitle($page->getTitle());
                $tree->setParentId($rootCmsTreeId);
                $tree->setPageId($page->getId());
                $tree->setIdentifier($page->getIdentifier());
                $tree->setPath($path);
                $tree->setPosition($position);
                $tree->setLevel(2);
                $this->treeRepository->save($tree);
                $position++;
            }
        }
    }
}
