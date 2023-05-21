<?php

namespace Woom\CmsTree\Model\Page;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Woom\CmsTree\Api\Data\TreeInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Cms\Model\Page;
use Magento\Framework\Exception\NoSuchEntityException;
use Woom\CmsTree\Model\ResourceModel\Page\Tree\Collection;

class Tree extends AbstractModel implements TreeInterface, IdentityInterface
{
    const CACHE_TAG = 'cms_tree';

    /**
     * Tree repository
     *
     * @var TreeRepository
     */
    private $treeRepository;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Woom\CmsTree\Model\ResourceModel\Page\Tree');
    }

    /**
     * Tree constructor.
     *
     * @param Context               $context
     * @param Registry              $registry
     * @param TreeRepository        $treeRepository
     * @param StoreManagerInterface $storeManager
     * @param AbstractResource|null $resource
     * @param AbstractDb|null       $resourceCollection
     * @param array                 $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        TreeRepository $treeRepository,
        StoreManagerInterface $storeManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->treeRepository = $treeRepository;
        $this->storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     *
     * @return int|null
     */
    public function getPageId()
    {
        return $this->getData(TreeInterface::PAGE_ID);
    }

    /**
     * {@inheritdoc}
     *
     * @return string|null
     */
    public function getIdentifier()
    {
        return $this->getData(TreeInterface::IDENTIFIER);
    }

    /**
     * {@inheritdoc}
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->getData(TreeInterface::TITLE);
    }

    /**
     * {@inheritdoc}
     *
     * @return string|null
     */
    public function getRequestUrl()
    {
        return $this->getData(TreeInterface::REQUEST_URL);
    }

    /**
     * {@inheritdoc}
     *
     * @return string|null
     */
    public function getPath()
    {
        return $this->getData(TreeInterface::PATH);
    }

    /**
     * {@inheritdoc}
     *
     * @return int|null
     */
    public function getPosition()
    {
        return $this->getData(TreeInterface::POSITION);
    }

    /**
     * {@inheritdoc}
     *
     * @return int|null
     */
    public function getLevel()
    {
        return $this->getData(TreeInterface::LEVEL);
    }

    /**
     * {@inheritdoc}
     *
     * @return int|null
     */
    public function getChildrenCount()
    {
        return $this->getData(TreeInterface::CHILDREN_COUNT);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function isInMenu()
    {
        return $this->getData(TreeInterface::IS_IN_MENU);
    }

    /**
     * {@inheritdoc}
     *
     * @return int|null
     */
    public function getMenuAddType()
    {
        return $this->getData(TreeInterface::MENU_ADD_TYPE);
    }

    /**
     * {@inheritdoc}
     *
     * @return int|null
     */
    public function getMenuAddCategoryId()
    {
        return $this->getData(TreeInterface::MENU_ADD_CATEGORY_ID);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getMenuLabel()
    {
        return $this->getData(TreeInterface::MENU_LABEL);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $parentId
     *
     * @return $this
     */
    public function setParentId($parentId)
    {
        return $this->setData(TreeInterface::PARENT_TREE_ID, $parentId);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $pageId
     *
     * @return $this
     */
    public function setPageId($pageId)
    {
        return $this->setData(TreeInterface::PAGE_ID, $pageId);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $identifier
     *
     * @return $this
     */
    public function setIdentifier($identifier)
    {
        return $this->setData(TreeInterface::IDENTIFIER, $identifier);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $requestUrl
     *
     * @return $this
     */
    public function setRequestUrl($requestUrl)
    {
        return $this->setData(TreeInterface::REQUEST_URL, $requestUrl);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        return $this->setData(TreeInterface::PATH, $path);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->setData(TreeInterface::TITLE, $title);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $position
     *
     * @return $this
     */
    public function setPosition($position)
    {
        return $this->setData(TreeInterface::POSITION, $position);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $level
     *
     * @return $this
     */
    public function setLevel($level)
    {
        return $this->setData(TreeInterface::LEVEL, $level);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $childrenCount
     *
     * @return $this
     */
    public function setChildrenCount($childrenCount)
    {
        return $this->setData(TreeInterface::CHILDREN_COUNT, $childrenCount);
    }

    /**
     * {@inheritdoc}
     *
     * @param bool $isInMenu
     *
     * @return $this
     */
    public function setIsIsMenu($isInMenu)
    {
        return $this->setData(TreeInterface::IS_IN_MENU, $isInMenu);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $menuAddType
     *
     * @return $this
     */
    public function setMenuAddType($menuAddType)
    {
        return $this->setData(TreeInterface::MENU_ADD_TYPE, $menuAddType);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $menuAddCategoryId
     *
     * @return $this
     */
    public function setMenuAddCategoryId($menuAddCategoryId)
    {
        return $this->setData(TreeInterface::MENU_ADD_CATEGORY_ID, $menuAddCategoryId);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $menuLabel
     *
     * @return $this
     */
    public function setMenuLabel($menuLabel)
    {
        return $this->setData(TreeInterface::MENU_LABEL, $menuLabel);
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [
            self::CACHE_TAG . '_' . $this->getId(),
        ];
        if (!$this->getId() || $this->hasDataChanges() || $this->isDeleted()) {
            $identities[] = self::CACHE_TAG;
            $identities[] = Page::CACHE_TAG . '_' . $this->getPageId();
            foreach ($this->getParentIds() as $parentId) {
                $identities[] = Page::CACHE_TAG . '_' . $parentId;
            }
        }

        return $identities;
    }

    /**
     * Get array of page ids which are part of page path
     * Result array contains id of current page because it is part of the path
     *
     * @return array
     */
    public function getPathIds()
    {
        $ids = $this->getData('path_ids');
        if ($ids === null) {
            $ids = explode('/', $this->getPath());
            $this->setData('path_ids', $ids);
        }

        return $ids;
    }

    /**
     * Get all parent page ids
     *
     * @return array
     */
    public function getParentIds()
    {
        return array_diff($this->getPathIds(), [$this->getId()]);
    }

    /**
     * Move page
     *
     * @param  int      $parentId    new parent tree id
     * @param  null|int $afterTreeId tree id after which we have put current page
     *
     * @return $this
     * @throws LocalizedException|\Exception
     */
    public function move($parentId, $afterTreeId)
    {
        /**
         * Validate new parent page id. (page model is used for backward
         * compatibility in event params)
         */
        try {
            $parent = $this->treeRepository->getById($parentId);
        } catch (NoSuchEntityException $e) {
            throw new LocalizedException(
                __(
                    'Sorry, but we can\'t find the new parent tree you selected.'
                ),
                $e
            );
        }

        if (!$this->getId()) {
            throw new LocalizedException(
                __('Sorry, but we can\'t find the new tree you selected.')
            );
        } elseif ($parent->getId() == $this->getId()) {
            throw new LocalizedException(
                __(
                    'We can\'t move the tree because the parent tree name matches the child tree name.'
                )
            );
        }

        $this->setMovedPageId($this->getId());

        $this->_getResource()->beginTransaction();
        try {
            $this->getResource()->changeParent($this, $parent, $afterTreeId);
            $this->_getResource()->commit();
        } catch (\Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }

        $this->_eventManager->dispatch('clean_cache_by_tags', ['object' => $this]);
        $this->_cacheManager->clean([Page::CACHE_TAG]);

        return $this;
    }

    /**
     * Get parent page identifier
     *
     * @return int
     */
    public function getParentId()
    {
        $parentId = $this->getData(TreeInterface::PARENT_TREE_ID);
        if (isset($parentId)) {
            return $parentId;
        }
        $parentIds = $this->getParentIds();

        return intval(array_pop($parentIds));
    }

    /**
     * Get first child for provided parent tree
     *
     * @param int $parentTreeId
     *
     * @return $this
     * @throws LocalizedException
     */
    public function getFirstChild($parentTreeId)
    {
        $this->_getResource()->getFirstChild($this, $parentTreeId);
        $this->_afterLoad();
        $this->setOrigData();

        return $this;
    }

    /**
     * Get direct children trees for this tree
     *
     * @return Collection
     * @throws LocalizedException
     */
    public function getDirectChildren()
    {
        $children = $this->_getResource()->getDirectChildren($this);

        return $children;
    }

    /**
     * Get child page ids for this tree
     *
     * @return array
     * @throws LocalizedException
     */
    public function getChildPageIds()
    {
        $childPageIds = $this->_getResource()->getChildPageIds($this);

        return $childPageIds;
    }

    /**
     * Get tree by request url
     *
     * @param string $url
     * @param int    $storeId
     *
     * @return $this
     * @throws LocalizedException
     */
    public function getByRequestUrl($url, $storeId)
    {
        $this->_getResource()->getByRequestUrl($this, $url, $storeId);
        $this->_afterLoad();
        $this->setOrigData();

        return $this;
    }

    /**
     * Retrieve Page URL
     *
     * @param int|null $store
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getUrl($store = null)
    {
        return $this->storeManager->getStore($store)->getUrl('', ['_direct' => trim($this->getRequestUrl())]);
    }
}
