<?php

namespace Woom\CmsTree\Block\Adminhtml\Page;

use Magento\Backend\Block\Template\Context;
use Woom\CmsTree\Model\ResourceModel\TreeBlock as BlockModel;
use Magento\Framework\Registry;
use Magento\Cms\Model\PageFactory;
use Woom\CmsTree\Model\Page\TreeFactory;
use Magento\Framework\DB\Helper;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Json\EncoderInterface;
use Woom\CmsTree\Model\ResourceModel\Page\Tree\CollectionFactory;
use Woom\CmsTree\Api\Data\TreeInterface;
use Magento\Store\Model\Store;
use Woom\CmsTree\Model\ResourceModel\Page\Tree\Collection;

class TreeBlock extends AbstractTreeBlock
{
    /**
     * Event object
     *
     * @var string
     */
    protected $eventObject = 'adminhtml_cms_page_tree';

    /**
     * Object name
     *
     * @var string
     */
    protected $objectName = 'tree';

    /**
     * Node id variable name
     *
     * @var string
     */
    protected $nodeIdVar = 'tree_id';

    /**
     * Node title variable name
     *
     * @var string
     */
    protected $nodeTitleVar = 'title';

    /**
     * Add button label
     *
     * @var string
     */
    protected $addButtonLabel = 'Add New Page';

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'Woom_CmsTree::page/treeblock.phtml';

    /**
     * DB resource helper
     *
     * @var Helper
     */
    protected $resourceHelper;

    /**
     * CMS Page factory
     *
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * CMS Page Tree factory
     *
     * @var TreeFactory
     */
    protected $treeFactory;

    /**
     * Tree collection factory
     *
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Edit url cache
     *
     * @var string
     */
    protected $editUrl;

    /**
     * TreeBlock constructor.
     *
     * @param Context           $context
     * @param BlockModel        $blockModel
     * @param Registry          $registry
     * @param PageFactory       $pageFactory
     * @param TreeFactory       $treeFactory
     * @param Helper            $resourceHelper
     * @param Session           $session
     * @param EncoderInterface  $jsonEncoder
     * @param CollectionFactory $collectionFactory
     * @param array             $data
     */
    public function __construct(
        Context $context,
        BlockModel $blockModel,
        Registry $registry,
        PageFactory $pageFactory,
        TreeFactory $treeFactory,
        Helper $resourceHelper,
        Session $session,
        EncoderInterface $jsonEncoder,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->resourceHelper = $resourceHelper;
        $this->pageFactory = $pageFactory;
        $this->treeFactory = $treeFactory;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $blockModel, $registry, $session, $jsonEncoder, $data);
    }

    /**
     * Retrieve current page instance
     *
     * @return array|null
     */
    public function getPageRegistry()
    {
        return $this->coreRegistry->registry('cms_page');
    }

    /**
     * Get page id
     *
     * @return int|string|null
     */
    public function getPageId()
    {
        if ($this->getPageRegistry()) {
            return $this->getPageRegistry()->getId();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootId()
    {
        return TreeInterface::TREE_ROOT_ID;
    }

    /**
     * {@inheritdoc}
     */
    public function getTreeId()
    {
        if ($this->getObjectRegistry()) {
            return $this->getObjectRegistry()->getId();
        }

        return TreeInterface::TREE_ROOT_ID;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootIds()
    {
        $ids = $this->getData('root_ids');
        if ($ids === null) {
            $ids = [$this->getRootId()];
            foreach ($this->_storeManager->getStores() as $store) {
                $ids[] = $store->getRootCmsTreeId();
            }
            $this->setData('root_ids', $ids);
        }

        return $ids;
    }

    /**
     * {@inheritdoc}
     */
    public function getEditUrl()
    {
        if (!$this->editUrl) {
            $this->editUrl = $this->getUrl(
                'cmstree/page/edit',
                ['store' => null, '_query' => false, 'id' => null, 'parent' => null]
            );
        }
        return $this->editUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getMoveUrl()
    {
        return $this->getUrl('cmstree/page/move', ['store' => $this->getRequest()->getParam('store')]);
    }

    /**
     * {@inheritdoc}
     */
    protected function isParent($node)
    {
        if ($node && $this->getTreeRegistry()) {
            $pathIds = $this->getTreeRegistry()->getPathIds();

            if (in_array($node->getId(), $pathIds)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function isRootForStores($node)
    {
        return in_array($node->getTreeId(), $this->getRootIds());
    }

    /**
     * {@inheritdoc}
     */
    protected function overrideNodeParams($item, $node)
    {
        $item['id'] = (int)$node->getTreeId();
        $item['tree_id'] = (int)$node->getTreeId();
        $item['page_id'] = (int)$node->getPageId();

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    protected function getCollection()
    {
        $storeId = $this->getRequest()->getParam('store', Store::DEFAULT_STORE_ID);
        $collection = $this->getData('collection');
        if ($collection === null) {
            /** @var Collection $collection */
            $collection = $this->collectionFactory->create();

            $collection->addCmsActiveColumn()->addStoreFilter(
                $storeId
            );

            $this->setData('collection', $collection);
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    protected function getRootIdByStoreId($storeId)
    {
        if ($storeId) {
            $store = $this->_storeManager->getStore($storeId);
            $rootId = $store->getRootCmsTreeId();
        } else {
            $rootId = $this->_storeManager->getStore(Store::ADMIN_CODE)->getRootCmsTreeId();
        }

        return $rootId;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAdditionalDataCollection()
    {
        $storeId = $this->getRequest()->getParam('store', Store::DEFAULT_STORE_ID);
        $collection = $this->getData('additional_data_collection');
        if ($collection === null) {
            $collection = $this->treeFactory->create()->getCollection();

            $collection->addStoreFilter(
                $storeId
            );

            $this->setData('additional_data_collection', $collection);
        }

        return $collection;
    }
}
