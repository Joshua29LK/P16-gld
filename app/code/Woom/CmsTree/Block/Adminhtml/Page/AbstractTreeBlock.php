<?php

namespace Woom\CmsTree\Block\Adminhtml\Page;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Tree\Dbp as TreeDbpModel;
use Magento\Framework\Registry;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Data\Tree\Node;
use Magento\Backend\Block\Widget\Button;
use Magento\Framework\DataObject;
use Magento\Framework\Data\Tree as DataTree;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class AbstractTreeBlock extends Template
{
    /**
     * Event object
     *
     * @var string
     */
    protected $eventObject = 'adminhtml_tree';

    /**
     * Object name
     *
     * @var string
     */
    protected $objectName = 'node';

    /**
     * Node id variable name
     *
     * @var string
     */
    protected $nodeIdVar = 'id';

    /**
     * Node title variable name
     *
     * @var string
     */
    protected $nodeTitleVar = 'title';

    /**
     * Node children variable name
     *
     * @var string
     */
    protected $nodeChildrenVar = 'children_count';

    /**
     * Add button label
     *
     * @var string
     */
    protected $addButtonLabel = 'Add Node';

    /**
     * Tree DBP model
     *
     * @var TreeDbpModel
     */
    protected $treeDbpModel;

    /**
     * Core registry
     *
     * @var Registry|null
     */
    protected $coreRegistry = null;

    /**
     * Session
     *
     * @var Session
     */
    protected $session;

    /**
     * Json encoder
     *
     * @var EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * Edit url cache
     *
     * @var string
     */
    protected $editUrl;

    /**
     * AbstractTree constructor.
     *
     * @param Context          $context
     * @param TreeDbpModel     $treeDbpModel
     * @param Registry         $registry
     * @param Session          $session
     * @param EncoderInterface $jsonEncoder
     * @param array            $data
     */
    public function __construct(
        Context $context,
        TreeDbpModel $treeDbpModel,
        Registry $registry,
        Session $session,
        EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->treeDbpModel = $treeDbpModel;
        $this->coreRegistry = $registry;
        $this->session = $session;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $data);
    }

    /**
     * Prepare block layout
     * add "add object" button
     *
     * @return Template
     */
    protected function _prepareLayout()
    {
        $label = $this->addButtonLabel;
        $addUrl = $this->getUrl("*/*/add", ['_current' => false, 'id' => null, '_query' => false]);
        $this->addChild(
            'add_sub_button',
            Button::class,
            [
                'label'   => __($label),
                'onclick' => "addNew('".$addUrl."', false)",
                'class'   => 'add',
                'id'      => 'add_sub_button',
                'style'   => $this->canAddSub() ? '' : 'display: none;'
            ]
        );

        return parent::_prepareLayout();
    }

    /**
     * Get root tree
     *
     * @param mixed|null $parentNode
     * @param int        $recursionLevel
     *
     * @return Node|array|null
     */
    public function getRoot($parentNode = null, $recursionLevel = 3)
    {
        if ($parentNode !== null && $parentNode->getId()) {
            return $this->getNode($parentNode, $recursionLevel);
        }
        $root = $this->coreRegistry->registry('root');
        if ($root === null) {
            $storeId = (int)$this->getRequest()->getParam('store');
            $rootId = $this->getRootIdByStoreId($storeId);
            $tree = $this->treeDbpModel->load(null, $recursionLevel);

            if ($this->getObjectRegistry()) {
                $tree->loadEnsuredNodes($this->getObjectRegistry(), $tree->getNodeById($rootId));
            }

            $tree->addCollectionData($this->getCollection());

            $root = $tree->getNodeById($rootId);

            if ($root && $rootId != $this->getRootId()) {
                $root->setIsVisible(true);
            } elseif ($root && $root->getId() == $this->getRootId()) {
                $root->setTitle(__('Root'));
            }

            $this->coreRegistry->register('root', $root);
        }

        return $root;
    }

    /**
     * Get node from DBP model
     *
     * @param mixed $parentNode
     * @param int   $recursionLevel
     *
     * @return Node
     */
    protected function getNode($parentNode, $recursionLevel = 2)
    {
        $nodeId = $parentNode->getId();
        $node = $this->treeDbpModel->loadNode($nodeId);
        $node->loadChildren($recursionLevel);

        if ($node && $nodeId != $this->getRootId()) {
            $node->setIsVisible(true);
        } elseif ($node && $node->getId() == $this->getRootId()) {
            $node->setTitle(__('Root'));
        }

        $this->treeDbpModel->addCollectionData($this->getAdditionalDataCollection());

        return $node;
    }

    /**
     * Get object from registry
     *
     * @return mixed
     */
    public function getObjectRegistry()
    {
        return $this->coreRegistry->registry($this->objectName);
    }

    /**
     * Get store from params
     *
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store');

        return $this->_storeManager->getStore($storeId);
    }

    /**
     * Get root by store id
     *
     * @param int $storeId
     *
     * @return int
     */
    protected function getRootIdByStoreId($storeId)
    {
        return $this->getRootId();
    }

    /**
     * Get data collection
     *
     * @return AbstractCollection
     */
    protected function getCollection()
    {
        $collection = $this->getData('collection');

        return $collection;
    }

    /**
     * Get additional data collection
     *
     * @return AbstractCollection
     */
    protected function getAdditionalDataCollection()
    {
        $collection = $this->getData('additional_data_collection');

        return $collection;
    }

    /**
     * Get node edit url
     *
     * @return string
     */
    public function getEditUrl()
    {
        if (!$this->editUrl) {
            $this->editUrl = $this->getUrl(
                '*/*/edit',
                ['store' => null, '_query' => false, 'id' => null, 'parent' => null]
            );
        }
        return $this->editUrl;
    }

    /**
     * Get root node id
     *
     * @return int
     */
    public function getRootId()
    {
        return 0;
    }

    /**
     * Return ids of root pages as array
     *
     * @return mixed
     */
    public function getRootIds()
    {
        $ids = $this->getData('root_ids');

        return $ids;
    }

    /**
     * Get url to load tree from
     *
     * @param bool|null $expanded
     *
     * @return string
     */
    public function getLoadTreeUrl($expanded = null)
    {
        $params = ['_current' => true, 'id' => null, 'store' => null];
        if ($expanded == null && $this->session->getIsTreeWasExpanded() || $expanded == true) {
            $params['expand_all'] = true;
        }

        return $this->getUrl('*/*/treeJson', $params);
    }

    /**
     * Check if tree is expanded
     *
     * @return boolean|null
     */
    public function isTreeExpanded()
    {
        return $this->session->getIsTreeExpanded();
    }

    /**
     * Get page move url
     *
     * @return string
     */
    public function getMoveUrl()
    {
        return $this->getUrl('*/*/move', ['store' => $this->getRequest()->getParam('store')]);
    }

    /**
     * Get tree with children
     *
     * @param mixed|null $parentNodePage
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getTree($parentNodePage = null)
    {
        $rootArray = $this->getNodeJson($this->getRoot($parentNodePage));
        $tree = isset($rootArray['children']) ? $rootArray['children'] : [];

        return $tree;
    }

    /**
     * Get JSON-encoded tree
     *
     * @param mixed|null $parentNodePage
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getTreeJson($parentNodePage = null)
    {
        $rootArray = $this->getNodeJson($this->getRoot($parentNodePage));
        $json = $this->jsonEncoder->encode(isset($rootArray['children']) ? $rootArray['children'] : []);

        return $json;
    }

    /**
     * Get JSON of a tree node or an associative array
     *
     * @param Node|array $node
     * @param int        $level
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @throws NoSuchEntityException
     */
    protected function getNodeJson($node, $level = 0)
    {
        // create a node from data array
        if (is_array($node)) {
            $node = new Node($node, $this->nodeIdVar, new DataTree());
        }

        $rootForStores = $this->isRootForStores($node);
        $allowMove = $this->isMovable($node);

        $item = [];
        $item['text'] = $this->buildNodeName($node);
        $item['id'] = $node->getId();
        $item['store'] = (int)$this->getStore()->getId();
        $item['path'] = $node->getData('path');
        $item['cls'] = 'folder '.($node->getIsActive() ? 'active-category' : 'no-active-category');
        $item['allowDrop'] = $allowMove;
        $item['allowDrag'] = $allowMove && ($node->getLevel() == 1 && $rootForStores ? false : true);

        if ((int)$node->getDataUsingMethod($this->nodeChildrenVar) > 0) {
            $item['children'] = [];
        }

        $isParent = $this->isParent($node);
        if ($isParent || $node->getLevel() < 2) {
            $item['expanded'] = true;
        }

        $item = $this->overrideNodeParams($item, $node);

        if ($node->hasChildren()) {
            $item['children'] = [];
            foreach ($node->getChildren() as $child) {
                $item['children'][] = $this->getNodeJson($child, $level + 1);
            }
        }

        return $item;
    }

    /**
     * Method to allow for override of node parameters
     *
     * @param array $item
     * @param Node  $node
     *
     * @return array
     */
    protected function overrideNodeParams($item, $node)
    {
        return $item;
    }

    /**
     * Check if node is root for stores
     *
     * @param Node $node
     *
     * @return bool
     */
    protected function isRootForStores($node)
    {
        return in_array($node->getEntityId(), $this->getRootIds());
    }

    /**
     * Get node name
     *
     * @param Node $node
     *
     * @return string
     */
    protected function buildNodeName($node)
    {
        $result = $this->escapeHtml($node->getDataUsingMethod($this->nodeTitleVar));
        $result .= ' ('.$node->getDataUsingMethod($this->nodeChildrenVar).')';

        return $result;
    }

    /**
     * Check if sub object can be added
     *
     * @return boolean
     * @throws NoSuchEntityException
     */
    protected function canAddSub()
    {
        $options = new DataObject(['is_allow' => true]);
        $this->_eventManager->dispatch(
            $this->eventObject.'_can_add_sub',
            ['object' => $this, 'options' => $options, 'store' => $this->getStore()->getId()]
        );

        return $options->getIsAllow();
    }

    /**
     * Check if object is movable
     *
     * @param Node|array $node
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    protected function isMovable($node)
    {
        $options = new DataObject(['is_moveable' => true, 'node' => $node]);

        $this->_eventManager->dispatch(
            $this->eventObject.'_is_moveable',
            ['object' => $this, 'options' => $options, 'store' => $this->getStore()->getId()]
        );

        return $options->getIsMoveable();
    }

    /**
     * Check if node is parent
     *
     * @param Node $node
     *
     * @return bool
     */
    protected function isParent($node)
    {
        if ($node->hasChildren()) {
            return true;
        }

        return false;
    }

    /**
     * Check if object loaded by outside link to object edit
     *
     * @return boolean
     */
    public function isClearEdit()
    {
        return (bool)$this->getRequest()->getParam('clear');
    }

    /**
     * Add subpage button html
     *
     * @return string
     */
    public function getAddSubButtonHtml()
    {
        return $this->getChildHtml('add_sub_button');
    }
}
