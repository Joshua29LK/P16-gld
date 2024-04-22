<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace Magedelight\Megamenu\Model;

use Magedelight\Megamenu\Api\MegamenuManagementInterface;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\DataObject;
use Magedelight\Megamenu\Helper\Data;
use Magento\Cms\Model\BlockFactory;
use Magento\Catalog\Api\CategoryListInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;

class MegamenuManagement implements MegamenuManagementInterface
{
    protected $dataObjectFactory;
    protected $menuFactory;
    protected $menuItemsFactory;
    protected $menuHelper;
    protected $customerRepositoryInterface;
    protected $storeManager;
    protected $primaryMenuId = 0;
    protected $group = 0;

    /**
     * @var \Magedelight\Megamenu\Model\Menu
     */
    protected $primaryMenu;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepositoryInterface;

    /**
     * @var \Magedelight\Megamenu\Helper\Category
     */
    protected $categoryHelper;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var BlockFactory
     */
    protected $blockFactory;
     /**
      * @var MenuCollectionFactory
      */
    protected $menuCollectionFactory;
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var CategoryListInterface
     */
    protected $categoryList;
    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * MegamenuManagement constructor.
     * @param DataObjectFactory $dataObjectFactory
     * @param Data $menuHelper
     * @param MenuFactory $menuFactory
     * @param \Magedelight\Megamenu\Model\MenuItemsFactory $menuItemsFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepositoryInterface
     * @param \Magedelight\Megamenu\Helper\Category $categoryHelper
     * @param Data $helper
     */
    public function __construct(
        DataObjectFactory $dataObjectFactory,
        Data $menuHelper,
        MenuFactory $menuFactory,
        MenuItemsFactory $menuItemsFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepositoryInterface,
        \Magedelight\Megamenu\Helper\Category $categoryHelper,
        \Magedelight\Megamenu\Helper\Data $helper,
        BlockFactory $blockFactory,
        \Magedelight\Megamenu\Model\ResourceModel\Menu\CollectionFactory $menuCollectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CategoryListInterface $categoryList,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->menuHelper = $menuHelper;
        $this->menuFactory = $menuFactory;
        $this->menuItemsFactory = $menuItemsFactory;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->storeManager = $storeManager;
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
        $this->categoryHelper = $categoryHelper;
        $this->helper = $helper;
        $this->blockFactory = $blockFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->categoryList = $categoryList;
        $this->menuCollectionFactory = $menuCollectionFactory;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * @param null $customerId
     * @return \Magedelight\Megamenu\Api\MegamenuInterface|DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMenuData($customerId = null)
    {
        if ($customerId) {
            $customer = $this->customerRepositoryInterface->getById($customerId);
            $this->group = $customer->getGroupId();
        }
        $result = $this->dataObjectFactory->create();
        $result->setData('menu', $this->getMegamenu());
        return $result;
    }

    /**
     * @param $menuId
     * @return DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMenuDataById($menuId, $customerId = null)
    {
        if ($customerId) {
            $customer = $this->customerRepositoryInterface->getById($customerId);
            $this->group = $customer->getGroupId();
        }
        $this->primaryMenuId = $menuId;
        $this->primaryMenu = $this->loadMenuById($this->primaryMenuId);
        $result = $this->dataObjectFactory->create();
        $result->setData('menu', $this->getMegamenu(true));
        return $result;
    }

    /**
     * @param null $parentId
     * @param null $sortOrder
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function loadMenuItems($parentId = null, $sortOrder = null, $id = null)
    {
        if ($id == null) {
            $id = $this->primaryMenuId;
        }
        $items = $this->menuItemsFactory->create()->getCollection();
        $items->addFieldToFilter('main_table.menu_id', $id);
        if ($parentId !== null) {
            $items->addFieldToFilter('item_parent_id', $parentId);
        }
        if ($sortOrder !== null) {
            $items->setOrder('sort_order', $sortOrder);
        }
        $items->getSelect()->joinLeft(
            ['join_table' => 'megamenu_menus'],
            'main_table.menu_id = join_table.menu_id',
            ['customer_groups' => 'join_table.customer_groups']
        );
        $items->addFieldToFilter('join_table.customer_groups', ['finset' => $this->group]);

        return $items;
    }

    /**
     * @param null $customerId
     * @param bool $skip
     * @return Menu
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMegamenu($skip = false)
    {
        if (!$skip) {
            $this->initMegaMenu();
        }
        if ($this->primaryMenu->getData('menu_type') == 1 && $this->primaryMenu->getData('is_active')) {
            $menuItems = $this->loadMenuItems(0);
            $level = 0;
            $this->primaryMenu->setData('menu_items', $this->setNormalMenuItems($menuItems, $level));
        }
        if ($this->primaryMenu->getData('menu_type') == 2 && $this->primaryMenu->getData('is_active')) {
            $this->primaryMenu->setData('menu_items', $this->setMegaMenuItems());
        }
        if ($this->primaryMenu->getData('store_id')) {
            $this->primaryMenu->setData('store_id', implode(',', $this->primaryMenu->getData('store_id')));
        }
        return $this->primaryMenu;
    }

    /**
     * @return Menu
     */
    public function loadMenuById($id)
    {
        $menu = $this->menuFactory->create()->load($id);
        return $menu;
    }

    /**
     * @param $customerId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function initMegaMenu()
    {
        $this->primaryMenuId = $this->setPrimaryMenuId();
        $this->primaryMenu = $this->loadMenuById($this->primaryMenuId);
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @param $customerId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setPrimaryMenuId()
    {
        $menu_id = $this->menuHelper->getConfig('magedelight/general/primary_menu');
        $menu = $this->loadMenuById($menu_id);
        $customerGroup = $this->group;
        $customerGroupsArray = [];
        if ($menu->getCustomerGroups() !== 0) {
            $customerGroupsArray = explode(',', trim($menu->getCustomerGroups() ?? ''));
            if (!in_array($customerGroup, $customerGroupsArray) || $menu->getIsActive() != 1) {
                $menu_id = '';
            }
        }
        if (empty($menu_id)) {
            $menuCollection = $this->menuFactory->create()->getCollection()
                ->addStoreFilter($this->getStoreId())
                ->addFieldToFilter('is_active', '1')
                ->addFieldToFilter('customer_groups', ['finset' => $customerGroup])
                ->setPageSize(1)
                ->setCurPage(1);
            foreach ($menuCollection as $singleCollection) {
                return $menu_id = $singleCollection->getMenuId();
            }
        }
        return $menu_id;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setMegaMenuItems()
    {
        $items = [];
        $menuItems = $this->loadMenuItems(null, 'ASC');
        $customerGroup = $this->group;
        /** @var \Magedelight\Megamenu\Model\MenuItems $menuItem */
        foreach ($menuItems as $key => $menuItem) {
            $menuItem->setData('item_link', $this->generateMenuUrl($menuItem));
            $menuItem->setData('category_columns', json_decode($menuItem->getData('category_columns') ?? '[]'));
            if ($menuItem->hasData('item_columns')) {
                $itemColumns = json_decode($menuItem->getData('item_columns') ?? '[]');
                if ($menuItem->getData('item_type') == 'megamenu') {

                    if (!empty($itemColumns)) {
                        foreach ($itemColumns as $k => $rowItems) {

                            if (isset($rowItems->item_rows)) {
                                $rowItemsData = $rowItems->item_rows;
                                foreach ($rowItemsData as $column) {
                                    if ($column->type == 'category') {
                                        if (!$this->isAllowPermission($column->value)) {
                                            unset($itemColumns[$k]);
                                        }
                                    }
                                }
                            } else {
                                $column = $rowItems;
                                if ($column->type == 'category') {
                                    if (!$this->isAllowPermission($column->value)) {
                                        unset($itemColumns[$k]);
                                    }
                                }
                            }
                        }
                    }
                }
                if ($itemColumns) {
                    $menuItem->setData('item_columns', array_values($itemColumns));
                } else {
                    $menuItem->setData('item_columns', $itemColumns);
                }
            }
            if ($menuItem->getData('item_type') == 'category' && $menuItem->getData('category_display') == "1") {
                $menuItem->setData('childrens', $this->categoryHelper->getCategoryTreeById($menuItem, $customerGroup));
            } else {
                $menuItem->setData('childrens', []);
            }
            if ($menuItem->getData('item_type') == 'category') {
                if (!$this->isAllowPermission($menuItem->getData('object_id'))) {
                    $menuItem->unsetData();
                }
            }
            $items[] = $menuItem->getData();
        }
        return array_filter($items);
    }

    /**
     * @param $parentId
     * @return int
     */
    public function hasChildrenItems($parentId)
    {
        $count = $this->loadMenuItems($parentId)->count();
        return $count;
    }

    /**
     * @param $menuItems
     * @param $level
     * @param $customerId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setNormalMenuItems($menuItems, $level)
    {
        $normalMenu = [];
        /** @var  $menuItem \Magedelight\Megamenu\Model\MenuItems */
        foreach ($menuItems as $menuItem) {
            $exclude = false;
            if ($menuItem->getData('item_type') == 'category') {
                if (!$this->isAllowPermission($menuItem->getData('object_id'))) {
                    $exclude = true;
                }
            }
            if (!$exclude) {
                $menuId = $menuItem->getData('item_id');
                $menuItem->setData('item_link', $this->generateMenuUrl($menuItem));
                $hasChildren = $this->hasChildrenItems($menuId);
                if ($hasChildren) {
                    $menuItems = $this->loadMenuItems($menuId);
                    $menuItem->setData('childrens', $this->setNormalMenuItems($menuItems, $level + 1));
                    $normalMenu[] = $menuItem->getData();
                } else {
                    $menuItem->setData('childrens', []);
                    $normalMenu[] = $menuItem->getData();
                }
            }
        }
        return array_filter($normalMenu);
    }

    /**
     * @param $menuItem \Magedelight\Megamenu\Model\MenuItems|\Magedelight\Megamenu\Api\Data\MenuItemsInterface
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function generateMenuUrl($menuItem)
    {
        $linkurl = $menuItem->getData('item_link');
        $url = '';
        if ($menuItem->getData('item_type') == "link" && !empty($linkurl)) {
            return $linkurl;
        }
        if ($menuItem->getData('item_type') == "category") {
            if ($this->getCategoryById($menuItem->getObjectId())) {
                $url = $this->getCategoryById($menuItem->getObjectId())->getUrl();
            }
        }
        if ($menuItem->getData('item_type') == "pages") {
            $url = $this->storeManager->getStore()->getBaseUrl() . $menuItem->getData('item_link');
        }
        return $url;
    }

    /**
     * @param $menuItem \Magedelight\Megamenu\Model\MenuItems|\Magedelight\Megamenu\Api\Data\MenuItemsInterface
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function generateMenuItemName($menuItem)
    {
        $name = '';
        if ($menuItem->getItemType() == "category") {
            if ($this->getCategoryById($menuItem->getObjectId())) {
                $name = $this->getCategoryById($menuItem->getObjectId())->getMdMenuTitle() ? $this->getCategoryById($menuItem->getObjectId())->getMdMenuTitle() : $this->getCategoryById($menuItem->getObjectId())->getName();
            }
        } else {
            $name = $menuItem->getItemName();
        }
        return $name;
    }

    /**
     * @param $id
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isAllowPermission($id)
    {
        if (!$this->helper->permissionEnabled()) {
            return true;
        }
        $customerGroup = $this->group;
        $excludeCategoryIds = $this->helper->getExcludeCategoryIds($customerGroup);
        if (in_array($id, $excludeCategoryIds)) {
            return false;
        }
        return true;
    }

    /**
     * @param $id
     * @return \Magento\Cms\Model\Block
     */
    public function loadCmsBlock($id)
    {
        return $this->blockFactory->create()->load($id);
    }

    /**
     * @param $id
     * @return \Magento\Catalog\Api\Data\CategoryInterface
     */
    public function getCategoryById($id)
    {
        try {
            return $this->categoryRepositoryInterface->get($id, $this->getStoreId());
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * @param $category
     * @return mixed
     */
    public function getChildrenCategories($category)
    {
        /** @var $category \Magento\Catalog\Model\Category */
        return $category->getChildrenCategories()
            ->addIsActiveFilter()
            ->addAttributeToFilter('include_in_menu', ['eq' => 1]);
    }
    /**
     * @param null $parentId
     * @param null $sortOrder
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function loadAllMegaMenus($parentId = null, $sortOrder = null, $id = null)
    {
        $customerGroup = $this->group;
        $menuCollection = $this->menuCollectionFactory->create();
        $menuCollection = $menuCollection->addStoreFilter($this->getStoreId())
                            ->addFieldToFilter('is_active', 1)
                            ->addFieldToFilter('customer_groups', ['finset' => $customerGroup]);
        return $menuCollection;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getChildrenCategoriesById($id, $sort = null)
    {
        // Get the parent category by its ID
        $parentCategory = $this->categoryRepositoryInterface->get($id);

        // Get the child categories of the parent category
        if ($sort) {
            $sortOrder = $this->sortOrderBuilder->setField($sort['sort_by'])->setDirection($sort['sort_order'])->create();
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('parent_id', $parentCategory->getId())
                ->addFilter('is_active', true)
                ->addFilter('include_in_menu', ['eq' => 1])
                ->setSortOrders([$sortOrder])
                ->create();
        } else {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('parent_id', $parentCategory->getId())
                ->addFilter('is_active', true)
                ->addFilter('include_in_menu', ['eq' => 1])
                ->create();
        }
        $childCategories = $this->categoryList->getList($searchCriteria);

        return $childCategories->getItems();
    }
}
