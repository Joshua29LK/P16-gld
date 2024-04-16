<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace Magedelight\Megamenu\Block;

use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Theme\Block\Html\Topmenu as MagentoTopmenu;
use Magedelight\Megamenu\Model\Menu;
use Magento\Framework\View\Element\TemplateFactory;

/**
 * Class Topmenu
 *
 * @package Magedelight\Megamenu\Block
 */
class Topmenu extends MagentoTopmenu
{
    const MEGA_MENU_TEMPLATE = 'Magedelight_Megamenu::menu/topmenu.phtml';
    const ALL_CATEGORY_MENU = 'all-category';
    const AMAZON_MENU = 'amazon-menu';
    const PRIMARY_NONE = 0;

    protected $registry;

    protected $helper;

    protected $customerSession;

    protected $megamenuManagement;

    protected $_page;

    /**
     * @var \Magedelight\Megamenu\Api\Data\ConfigInterface
     */
    public $primaryMenu;

    public $primaryMenuId = 0;

    public $categoryData;

    protected $mdColumnCount = 10;

    protected $getDescription;

    protected $output;

    protected $categoryRepository;

    public $allCategoryMenu;

    public $allCategoryMenuData = [];

    public $allCategoryMenuId = 0;
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;
    /**
     * @var TemplateFactory
     */
    protected $templateFactory;
    /**
     * Topmenu constructor.
     * @param Template\Context $context
     * @param NodeFactory $nodeFactory
     * @param TreeFactory $treeFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Model\SessionFactory $session
     * @param \Magento\Cms\Model\Page $page
     * @param \Magedelight\Megamenu\Helper\Data $helper
     * @param \Magedelight\Megamenu\Model\MegamenuManagement $megamenuManagement
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\SessionFactory $session,
        \Magento\Cms\Model\Page $page,
        \Magedelight\Megamenu\Helper\Data $helper,
        \Magedelight\Megamenu\Model\MegamenuManagement $megamenuManagement,
        \Magento\Catalog\Helper\Output $output,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        TemplateFactory $templateFactory,
        array $data = []
    ) {
        parent::__construct($context, $nodeFactory, $treeFactory, $data);
        $this->registry = $registry;
        $this->customerSession = $session;
        $this->helper = $helper;
        $this->megamenuManagement = $megamenuManagement;
        $this->_page = $page;
        $this->output = $output;
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;
        $this->templateFactory = $templateFactory;
    }

    /**
     * @return int
     */
    protected function getCacheLifetime()
    {
        return parent::getCacheLifetime() ?: 3600;
    }

    /**
     * Get cache key informative items
     *
     * @return array
     * @since 100.1.0
     */
    public function getCacheKeyInfo()
    {
        $keyInfo = parent::getCacheKeyInfo();
        $keyInfo[] = $this->getUrl('*/*/*', ['_current' => true, '_query' => '']);
        return $keyInfo;
    }

    /**
     * Get tags array for saving cache
     *
     * @return array
     * @since 100.1.0
     */
    protected function getCacheTags()
    {
        return array_merge(parent::getCacheTags(), $this->getIdentities());
    }

    /**
     * @return mixed
     */
    public function getCurrentCat()
    {
        $category = $this->registry->registry('current_category');
        if (isset($category) and ! empty($category->getId())) {
            return $category->getId();
        }
        return '';
    }

    /**
     * @return int
     */
    public function getCurentPage()
    {
        if ($this->_page->getId()) {
            return $pageId = $this->_page->getId();
        }
        return '';
    }

    /**
     * @param $template
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setCustomTemplate($template)
    {
        $this->setTemplate($template);
        if ($this->helper->isEnabled()) {
            $_customerSession = $this->customerSession->create();
            if ($_customerSession->isLoggedIn()) {
                $this->primaryMenu = $this->megamenuManagement->getMenuData($_customerSession->getCustomerId())->getMenu();
            } else {
                $this->primaryMenu = $this->megamenuManagement->getMenuData()->getMenu();
            }
            $this->primaryMenuId = $this->primaryMenu->getMenuId();

            if ($this->primaryMenu->getIsActive()) {
                if ($this->primaryMenu->getMenuType() == Menu::MEGA_MENU) {
                    $this->setTemplate(self::MEGA_MENU_TEMPLATE);
                }
            } elseif ($this->helper->isHumbergerMenu()) {
                $this->setTemplate(self::MEGA_MENU_TEMPLATE);
            }
        }
    }

    /**
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getHtml($outermostClass = '', $childrenWrapClass = '', $limit = 0)
    {
        return $this->getMegaMenuHtml($outermostClass, $childrenWrapClass, $limit);
    }
    /**
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAllCategoryMenuHtml($menuId, $outermostClass = '', $childrenWrapClass = '', $limit = 0)
    {
        return $this->getAllCategoryMegaMenuHtml($menuId, $outermostClass, $childrenWrapClass, $limit);
    }
    /**
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAllCategoryRightMenuHtml($menuId, $outermostClass = '', $childrenWrapClass = '', $limit = 0)
    {
        return $this->getAllCategoryRightMegaMenuHtml($menuId, $outermostClass, $childrenWrapClass, $limit);
    }

    /**
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBurgerHtml($outermostClass = '', $childrenWrapClass = '', $limit = 0)
    {
        return $this->getHumbergerMenuHtml($outermostClass, $childrenWrapClass, $limit);
    }

    /**
     * @param $outermostClass
     * @param $childrenWrapClass
     * @param $limit
     * @return string
     */
    public function getHumbergerMenuHtml($outermostClass, $childrenWrapClass, $limit)
    {
        $this->_eventManager->dispatch(
            'page_block_html_topmenu_gethtml_before',
            ['menu' => $this->_menu, 'block' => $this,'request' => $this->getRequest()]
        );

        $this->_menu->setOutermostClass($outermostClass);
        $this->_menu->setChildrenWrapClass($childrenWrapClass);

        $html = $this->_getHtml($this->_menu, $childrenWrapClass, $limit);

        $transportObject = new \Magento\Framework\DataObject(['html' => $html]);

        $this->_eventManager->dispatch(
            'page_block_html_topmenu_gethtml_after',
            ['menu' => $this->_menu, 'transportObject' => $transportObject]
        );
        $html = $transportObject->getHtml();
        return $html;
    }
    /**
     * @param $outermostClass
     * @param $childrenWrapClass
     * @param $limit
     * @return string
     */
    public function getAmazonMenuHtml()
    {
        $amazonMenu = $this->megamenuManagement->loadAllMegaMenus()
                            ->addFieldToFilter('menu_design_type', self::AMAZON_MENU)
                            ->getFirstItem();
        if ($amazonMenu) {
            $amazonMenuId = $amazonMenu->getMenuId();
            $amazonMenuItems = $this->megamenuManagement->loadMenuItems(0, 'ASC', $amazonMenuId);
        }
        $amazonMenuArray = [];
        $level = 0;
        foreach ($amazonMenuItems as $menuItem) {
            $itemData = [];
            $itemData['item_name'] = $menuItem->getItemName();
            $itemData['item_type'] = $menuItem->getItemType();
            $itemData['category_id'] = $menuItem->getObjectId();
            $itemData['category_display'] = $menuItem->getCategoryDisplay();
            $itemData['item_font_icon'] = $menuItem->getItemFontIcon();

            if ($menuItem->getItemType() == 'pages' || $menuItem->getItemType() == 'link') {
                $itemData['item_link'] = $menuItem->getItemLink();
            } else {
                $itemData['item_link'] = $this->megamenuManagement->generateMenuUrl($menuItem) ? : '#';
            }
            $itemData['open_in_newtab_text'] = '';

            if ((int) $menuItem->getOpenInNewTab()) {
                $itemData['open_in_newtab_text'] = 'target="_blank"';
            }
            if ($menuItem->getItemType() == 'category' && $menuItem->getCategoryDisplay()) {
                $_category = $this->megamenuManagement->getCategoryById($menuItem->getObjectId());

                $itemData['vertical_cat_exclude'] = $menuItem->getVerticalCatExclude();
                $itemData['item_label'] = $_category->getMdLabel() ?? '';
                $itemData['item_label_color'] = $_category->getMdLabelTextColor();
                $itemData['item_label_bg_color'] = $_category->getMdLabelBackgroundColor();
                $itemData['item_label_shape'] = $_category->getMdLabelShape();

                $childCategories = $this->megamenuManagement->getChildrenCategories($_category);
                $childCategoryData = [];
                $childCategoryTempData = [];
                foreach ($childCategories as $childCategory) {
                    if (!in_array(
                        $childCategory->getEntityId(),
                        explode(',', $itemData['vertical_cat_exclude'] ?? '')
                    )) {
                        $childCategory = $this->megamenuManagement->getCategoryById($childCategory->getEntityId());
                        $level = 1;
                        $childCategoryTempData['category_id'] = $childCategory->getEntityId();
                        $childCategoryTempData['item_name'] = $childCategory->getName();
                        $childCategoryTempData['item_link'] = $childCategory->getUrl();
                        $childCategoryTempData['level'] = $level;
                        $childCategoryTempData['item_class'] = 'md-amazon-title';

                        $childCategoryTempData['item_label'] = $childCategory->getMdLabel() ?? '';
                        $childCategoryTempData['item_label_color'] = $childCategory->getMdLabelTextColor();
                        $childCategoryTempData['item_label_bg_color'] = $childCategory->getMdLabelBackgroundColor();
                        $childCategoryTempData['item_label_shape'] = $childCategory->getMdLabelShape();

                        $childCategoryData[] = $childCategoryTempData;
                        $subChildCategories = $this->megamenuManagement->getChildrenCategories($childCategory);
                        foreach ($subChildCategories as $subChildCategory) {
                            if (!in_array(
                                $subChildCategory->getEntityId(),
                                explode(',', $itemData['vertical_cat_exclude'] ?? '')
                            )) {
                                $subChildCategory = $this->megamenuManagement->getCategoryById($subChildCategory->getEntityId());
                                $level = 2;
                                $childCategoryTempData['category_id'] = $subChildCategory->getEntityId();
                                $childCategoryTempData['item_name'] = $subChildCategory->getName();
                                $childCategoryTempData['item_link'] = $subChildCategory->getUrl();
                                $childCategoryTempData['level'] = $level;
                                $childCategoryTempData['item_class'] = 'child-level-3';

                                $childCategoryTempData['item_label'] = $subChildCategory->getMdLabel() ?? '';
                                $childCategoryTempData['item_label_color'] = $subChildCategory->getMdLabelTextColor();
                                $childCategoryTempData['item_label_bg_color'] = $subChildCategory->getMdLabelBackgroundColor();
                                $childCategoryTempData['item_label_shape'] = $subChildCategory->getMdLabelShape();

                                $childCategoryData[] = $childCategoryTempData;
                            }
                        }
                    }
                }
                $itemData['child_category'] = $childCategoryData;
            }
            $itemData['item_class'] = 'amazon-menu-item '.'child-level-'.$level;
            $itemData['level'] = $level;

            if ($menuItem->getCategoryDisplay()) {
                $itemData['category_display'] = $menuItem->getCategoryDisplay();
            }
            $amazonMenuArray[] = $itemData;
        }
        $html = '';
        $html .= '<ul class="amz-menu md-translateX">';
        foreach ($amazonMenuArray as $key => $amazonMenu) {
            $liClass = '';
            if ($amazonMenu['category_display']) {
                $liClass .= 'md-amazon-parent';
            }
            $fontIcon = '<span class="megaitemicons">'.$amazonMenu['item_font_icon'].'</span>';
            $html .= '<li class='.$liClass.'>';
            $html .= '<a href="'.$amazonMenu['item_link'].'" '.$amazonMenu['open_in_newtab_text'].'
            data-menu-id="'.$amazonMenu['category_id'].'">' . $fontIcon . $amazonMenu['item_name'].'</a>';
            if (isset($amazonMenu['item_label']) && $amazonMenu['item_label'] != '') {
                $inlineStyle = 'style="color: '.$amazonMenu['item_label_color'].'; background-color:'.$amazonMenu['item_label_bg_color'].'"';
                $html .= '<span class="md-label-text '.$amazonMenu['item_label_shape'].'"'.$inlineStyle.'>'.$amazonMenu['item_label'].'</span>';
            }
            $html .= '</li>';
        }
        $html .= '</ul>';
        $childHtml = '';
        foreach ($amazonMenuArray as $key => $amazonMenu) {
            if (isset($amazonMenu['category_display']) && (int) $amazonMenu['category_display'] == 1) {
                $childMenuArray = $amazonMenu['child_category'];
                $liClass = '';
                $childHtml .= '<ul class="amz-menu md-translateX-right" data-menu-id="'.$amazonMenu['category_id'].'">';
                $childHtml .= '<li'.$liClass.'><a href="#" class="md-menu-back-btn">Main Menu</a></li>';
                foreach ($childMenuArray as $key => $childMenu) {
                    $childHtml .= '<li class='.$childMenu['item_class'].'>';
                    $childHtml .= '<a href="'.$childMenu['item_link'].'">'.$childMenu['item_name'].'</a>';
                    if (isset($childMenu['item_label']) && $childMenu['item_label'] != '') {
                        $inlineStyle = 'style="color: '.$childMenu['item_label_color'].'; background-color:'.$childMenu['item_label_bg_color'].'"';
                        $childHtml .= '<span class="md-label-text '.$childMenu['item_label_shape'].'"'.$inlineStyle.'>'.$childMenu['item_label'].'</span>';
                    }
                    $childHtml .= '</li>';
                }
                $childHtml .= '</ul>';
            }
        }
        $html .= $childHtml;
        $transportObject = new \Magento\Framework\DataObject(['html' => $html]);
        $html = $transportObject->getHtml();
        return $html;
    }
    /**
     * @param $outermostClass
     * @param $childrenWrapClass
     * @param $limit
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMegaMenuHtml($outermostClass, $childrenWrapClass, $limit)
    {
        $this->_eventManager->dispatch(
            'page_block_html_topmenu_gethtml_before',
            ['menu' => $this->_menu, 'block' => $this,'request' => $this->getRequest()]
        );
        $this->_menu->setOutermostClass($outermostClass);
        $this->_menu->setChildrenWrapClass($childrenWrapClass);

        $html = $this->_getHtml($this->_menu, $childrenWrapClass, $limit);

        if ($this->helper->isEnabled() && $this->isPrimaryMenuSelected()) {
            if ($this->primaryMenu->getIsActive()) {
                $menuItems = $this->megamenuManagement->loadMenuItems(0, 'ASC');
                if ($this->primaryMenu->getMenuType() == Menu::MEGA_MENU) {
                    $html = '';
                    foreach ($menuItems as $item) {
                        $childrenWrapClass = "level0 nav-1 first parent main-parent";
                        if ($this->isCategoryInactive($item)) {
                            continue;
                        }
                        $html .= $this->setMegamenu($item, $childrenWrapClass);
                    }
                } else {
                    $parent = 'root';
                    $level = 0;
                    $html = $this->setPrimaryMenu($menuItems, $level, $parent, $outermostClass);
                }
            }
        }
        $transportObject = new \Magento\Framework\DataObject(['html' => $html]);
        $this->_eventManager->dispatch(
            'page_block_html_topmenu_gethtml_after',
            ['menu' => $this->_menu, 'transportObject' => $transportObject]
        );
        $html = $transportObject->getHtml();
        return $html;
    }
    /**
     * @param $outermostClass
     * @param $childrenWrapClass
     * @param $limit
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAllCategoryMegaMenuHtml($menuId, $outermostClass, $childrenWrapClass, $limit)
    {
        $this->allCategoryMenu = $this->getAllCategoryMenuObj($menuId);
        if ($this->helper->isEnabled() && $this->allCategoryMenu->getIsActive()) {
            $menuItems = $this->megamenuManagement->loadMenuItems(0, 'ASC', $menuId); // static id
            if ($this->allCategoryMenu->getMenuType() == Menu::MEGA_MENU) {
                /* If Megamenu then It will display here */
                $html = '';
                foreach ($menuItems as $item) {
                    $childrenWrapClass = "level0 nav-1 first parent main-parent";
                    if ($this->isCategoryInactive($item)) {
                        continue;
                    }
                    $html .= $this->setAllCategoryLeftMegamenu($menuId, $item, $childrenWrapClass);
                }
            } else {
                $parent = 'root';
                $level = 0;
                $html = $this->setPrimaryMenu($menuItems, $level, $parent, $outermostClass, $menuId);
            }
        } else {
            $html = $this->_getHtml($this->_menu, $childrenWrapClass, $limit);
        }
        $transportObject = new \Magento\Framework\DataObject(['html' => $html]);
        $html = $transportObject->getHtml();
        return $html;
    }
    /**
     * @param $outermostClass
     * @param $childrenWrapClass
     * @param $limit
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAllCategoryRightMegaMenuHtml($menuId, $outermostClass, $childrenWrapClass, $limit)
    {
        $this->allCategoryMenu = $this->getAllCategoryMenuObj($menuId);
        if ($this->helper->isEnabled() && $this->allCategoryMenu->getIsActive()) {
            $menuItems = $this->megamenuManagement->loadMenuItems(0, 'ASC', $menuId); // static id
            if ($this->allCategoryMenu->getMenuType() == Menu::MEGA_MENU) {
                /* If Megamenu then It will display here */
                $html = '';
                foreach ($menuItems as $item) {
                    $childrenWrapClass = "level0 nav-1 first parent main-parent";
                    if ($this->isCategoryInactive($item)) {
                        continue;
                    }
                    $html .= $this->setAllCategoryRightMegamenu($item, $childrenWrapClass);
                }
            } else {
                $parent = 'root';
                $level = 0;
                $html = $this->setPrimaryMenu($menuItems, $level, $parent, $outermostClass);
            }
        } else {
            $html = $this->_getHtml($this->_menu, $childrenWrapClass, $limit);
        }
        $transportObject = new \Magento\Framework\DataObject(['html' => $html]);
        $html = $transportObject->getHtml();
        return $html;
    }

    /**
     * @param $menuItems
     * @param $level
     * @param $parent
     * @param $outermostClass
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setPrimaryMenu($menuItems, $level = 0, $parent = '', $outermostClass = '', $menuId = '')
    {
        $megaMenuItemData = [
            'menu_block' => $this,
            'menu_items' => $menuItems,
            'level' => $level,
            'parent_node' => $parent,
            'menu_management' => $this->megamenuManagement,
            'menu' => ($menuId) ? $this->megamenuManagement->loadMenuById($menuId) : ''
        ];
        /*$megaMenuItemBlock = $this->getLayout()->createBlock('Magento\Framework\View\Element\Template');*/
        $megaMenuItemBlock = $this->templateFactory->create();
        /** @var $megaMenuItemBlock \Magento\Framework\View\Element\Template */
        $megaMenuItemBlock->setData($megaMenuItemData);
        $megaMenuItemBlock->setTemplate('Magedelight_Megamenu::menu/items/primaryMenu.phtml');
        return trim(preg_replace('/\s\s+/', ' ', $megaMenuItemBlock->toHtml()));
    }

    /**
     * @param $item \Magedelight\Megamenu\Model\MenuItems|\Magedelight\Megamenu\Api\Data\MenuItemsInterface
     * @param $key
     * @param $value
     * @return mixed
     */
    public function getCmsBlockConfig($item, $key, $value)
    {
        $blockType = ['header','bottom','left','right'];
        if ($value == 'enable') {
            $initValue = 0;
        }
        if ($value == 'block') {
            $initValue = "";
        }
        if ($value == 'title') {
            $initValue = "0";
        }
        $config[$key] = [$value => $initValue];
        if ($item->getCategoryColumns()) {
            $categoryColumns = json_decode($item->getCategoryColumns());
            foreach ($categoryColumns as $categoryColumn) {
                foreach ($blockType as $type) {
                    if ($categoryColumn->type === $type) {
                        $config[$type] = [
                            'enable' => (int) $categoryColumn->enable,
                            'block' => $categoryColumn->value,
                            'title' => $categoryColumn->showtitle
                        ];
                    }
                }
            }
        }
        return $config[$key][$value];
    }

    /**
     * @param $item \Magedelight\Megamenu\Model\MenuItems|\Magedelight\Megamenu\Api\Data\MenuItemsInterface
     * @param $childrenWrapClass
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setMegamenu($item, $childrenWrapClass)
    {
        $html = '';
        $megaMenuItemData = [
            'menu_block' => $this,
            'menu_item' => $item,
            'menu_management' => $this->megamenuManagement,
            'primary_menu'=> $this->primaryMenu
        ];
        /*$megaMenuItemBlock = $this->getLayout()->createBlock('Magento\Framework\View\Element\Template');*/
        $megaMenuItemBlock = $this->templateFactory->create();
        /** @var $megaMenuItemBlock \Magento\Framework\View\Element\Template */
        $megaMenuItemBlock->setData($megaMenuItemData);
        if ($item->getItemType() == 'megamenu') {
            $megaMenuItemBlock->setTemplate('Magedelight_Megamenu::menu/items/megaMenuItemBlock.phtml');
            $html .= trim(preg_replace('/\s\s+/', ' ', $megaMenuItemBlock->toHtml()));
        } else {
            if ($this->primaryMenu->getMenuDesignType() == 'horizontal-vertical') {
                $megaMenuItemBlock->setTemplate('Magedelight_Megamenu::menu/items/horVerMenuItemBlock.phtml');
            } else {
                $megaMenuItemBlock->setTemplate('Magedelight_Megamenu::menu/items/menuItemBlock.phtml');
            }
            $html .= trim(preg_replace('/\s\s+/', ' ', $megaMenuItemBlock->toHtml()));
        }
        return $html;
    }
    /**
     * @param $item \Magedelight\Megamenu\Model\MenuItems|\Magedelight\Megamenu\Api\Data\MenuItemsInterface
     * @param $childrenWrapClass
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setAllCategoryLeftMegamenu($menuId, $item, $childrenWrapClass)
    {
        $html = '';
        $megaMenuItemData = [
            'menu_block' => $this,
            'menu_item' => $item,
            'menu_management' => $this->megamenuManagement,
            'menu_id' => $menuId
        ];
        /*$megaMenuItemBlock = $this->getLayout()->createBlock('Magento\Framework\View\Element\Template');*/
        /** @var $megaMenuItemBlock \Magento\Framework\View\Element\Template */
        $megaMenuItemBlock = $this->templateFactory->create();
        $megaMenuItemBlock->setData($megaMenuItemData);
        $megaMenuItemBlock->setTemplate('Magedelight_Megamenu::menu/items/allCategoryMenuItemBlockFirstLevel.phtml');
            $html .= trim(preg_replace('/\s\s+/', ' ', $megaMenuItemBlock->toHtml()));
        return $html;
    }
    /**
     * @param $item \Magedelight\Megamenu\Model\MenuItems|\Magedelight\Megamenu\Api\Data\MenuItemsInterface
     * @param $childrenWrapClass
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setAllCategoryRightMegamenu($item, $childrenWrapClass)
    {
        $html = '';
        $megaMenuItemData = [
            'menu_block' => $this,
            'menu_item' => $item,
            'menu_management' => $this->megamenuManagement
        ];
        /*$megaMenuItemBlock = $this->getLayout()->createBlock('Magento\Framework\View\Element\Template');*/
        /** @var $megaMenuItemBlock \Magento\Framework\View\Element\Template */
        $megaMenuItemBlock = $this->templateFactory->create();
        $megaMenuItemBlock->setData($megaMenuItemData);
        if ($item->getItemType() == 'megamenu') {
            $megaMenuItemBlock->setTemplate('Magedelight_Megamenu::menu/items/allCategoryMegaMenuItemBlock.phtml');
            $html .= trim(preg_replace('/\s\s+/', ' ', $megaMenuItemBlock->toHtml()));
        } else {
            $megaMenuItemBlock->setTemplate('Magedelight_Megamenu::menu/items/allCategoryMenuItemBlock.phtml');
            $html .= trim(preg_replace('/\s\s+/', ' ', $megaMenuItemBlock->toHtml()));
        }
        return $html;
    }

    /**
     * @return string
     */
    public function getMenuClass()
    {
        $class = "menu ";
        $class .= $this->primaryMenu->getMenuDesignType().' ';
        $class .= $this->primaryMenu->getMenuAlignment() ?? '';
        $class .= $this->primaryMenu->getIsSticky() == '1' ? 'stickymenu ' : '';
        return $class;
    }

    /**
     * @param $menuItem \Magedelight\Megamenu\Model\MenuItems|\Magedelight\Megamenu\Api\Data\MenuItemsInterface
     * @return string
     */
    public function getActiveClass($menuItem)
    {
        if ($menuItem->getItemType() == 'category') {
            if ($menuItem->getObjectId() == $this->getCurrentCat()) {
                return ' active';
            }
        } elseif ($menuItem->getItemType() == 'pages') {
            if ($menuItem->getObjectId() == $this->getCurentPage()) {
                return ' active';
            }
        }
        return '';
    }

    /**
     * @param $menuItems
     * @param $key
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getChildColumnForMenuType($menuItems, $key)
    {
        $megaMenuItemData = [
            'menu_block' => $this,
            'menu_items' => $menuItems,
            'items_key' => $key,
            'menu_management' => $this->megamenuManagement
        ];
        /*$megaMenuItemBlock = $this->getLayout()->createBlock('Magento\Framework\View\Element\Template');*/
        /** @var $megaMenuItemBlock \Magento\Framework\View\Element\Template */
        $megaMenuItemBlock = $this->templateFactory->create();
        $megaMenuItemBlock->setData($megaMenuItemData);
        $megaMenuItemBlock->setTemplate('Magedelight_Megamenu::menu/items/megaMenuItemBlock/typeMenu.phtml');
        return trim(preg_replace('/\s\s+/', ' ', $megaMenuItemBlock->toHtml()));
    }

    /**
     * @param $menuItems
     * @param $key
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getChildColumnForMenuTypeBlock($menuItems, $key)
    {
        $megaMenuItemData = [
            'menu_block' => $this,
            'menu_items' => $menuItems,
            'items_key' => $key,
            'menu_management' => $this->megamenuManagement
        ];
        /*$megaMenuItemBlock = $this->getLayout()->createBlock('Magento\Framework\View\Element\Template');*/
        /** @var $megaMenuItemBlock \Magento\Framework\View\Element\Template */
        $megaMenuItemBlock = $this->templateFactory->create();
        $megaMenuItemBlock->setData($megaMenuItemData);
        $megaMenuItemBlock->setTemplate('Magedelight_Megamenu::menu/items/megaMenuItemBlock/typeBlock.phtml');
        return trim(preg_replace('/\s\s+/', ' ', $megaMenuItemBlock->toHtml()));
    }

    /**
     * @param $menuItems
     * @param $key
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getChildColumnForMenuTypeCategory($menuItems, $key)
    {
        $category = $this->megamenuManagement->getCategoryById($menuItems[$key]->value);
        if ($category) {
            $megaMenuItemData = [
                'menu_block' => $this,
                'menu_items' => $menuItems,
                'items_key' => $key,
                'menu_management' => $this->megamenuManagement,
                'category' => $category,
                'sub_category' => $this->megamenuManagement->getChildrenCategoriesById($category->getId())
            ];
            /*$megaMenuItemBlock = $this->getLayout()->createBlock('Magento\Framework\View\Element\Template');*/
            /** @var $megaMenuItemBlock \Magento\Framework\View\Element\Template */
            $megaMenuItemBlock = $this->templateFactory->create();
            $megaMenuItemBlock->setData($megaMenuItemData);
            $megaMenuItemBlock->setTemplate('Magedelight_Megamenu::menu/items/megaMenuItemBlock/typeCategory.phtml');
            return trim(preg_replace('/\s\s+/', ' ', $megaMenuItemBlock->toHtml()));
        }
    }

    /**
     * @param $menuItems
     * @param $key
     * @param $category
     * @param $subCats
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getChildColumnForSubCategory($menuItems, $key, $category, $subCats, $skipTitle = false, $level = 1)
    {
        $childHtml = '';
        if (!$skipTitle) {
            $categoryArray = $this->prepareCategoryItemsForMenuColumn($subCats, $menuItems[$key]);
        } else {
            $categoryArray = $subCats;
        }
        /** @var $category \Magento\Catalog\Model\Category */
        if ($menuItems[$key]->showtitle == '1' && !$skipTitle) {
            $childHtml .= '<h2>' . __($category->getName()) . '</h2>';
        }

        $childHtml .= '<ul class="child-column-megamenu-block child-level-'.$level.'">';
        foreach ($categoryArray as $cat) {
            $verticalclass = $cat['id'] == $this->getCurrentCat() ? 'active' : '';
            $liClass = count($cat['childrens']) > 0 ? 'cat-has-child' : 'cat-no-child';
            $childHtml .= '<li class="'.$liClass.' '.$verticalclass.'">';
            $childHtml .= '<a href="'.$cat['url'].'">'.__($cat['label']).'</a>';
            if (!empty($cat['childrens'])) {
                $childHtml .= $this->getChildColumnForSubCategory($menuItems, $key, $category, $cat['childrens'], true, $level+1);
            }
            $childHtml .= '</li>';
        }
        $childHtml .= '</ul>';
        return $childHtml;
    }

    /**
     * @param $subcats
     * @param $item
     * @param int $level
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function prepareCategoryItemsForMenuColumn($subcats, $item, $level = 1)
    {
        $leftArray = [];
        foreach ($subcats as $subcat) {
            $maxLevel = $item->categoryLevel ? (int) $item->categoryLevel : 2;
            if ($maxLevel < $level) {
                break;
            }
            //$_category = $this->megamenuManagement->getCategoryById($subcat->getId());
            $childrenCats = $this->megamenuManagement->getChildrenCategoriesById($subcat->getId());
            $group = [
                'id' => $subcat->getId(),
                'label' => $subcat->getName(),
                'url' => $subcat->getUrl(),
                'position' => $subcat->getPosition(),
                'childrens' => $this->prepareCategoryItemsForMenuColumn($childrenCats, $item, $level+1)
            ];
            $leftArray[] = $group;
        }
        return $this->sortByOrder($leftArray, $item);
    }

    /**
     * @param $categoryArray
     * @param $item
     * @return mixed
     */
    public function sortByOrder($categoryArray, $item)
    {
        usort($categoryArray, function ($x, $y) {
            return strcasecmp($x['position'], $y['position']);
        });
        if ($item->catSortBy && $item->catSortOrder) {
            if ($item->catSortBy == 'name' && $item->catSortOrder == 'asc') {
                usort($categoryArray, function ($x, $y) {
                    return strcasecmp($x['label'], $y['label']);
                });
            }
            if ($item->catSortBy == 'name' && $item->catSortOrder == 'desc') {
                usort($categoryArray, function ($x, $y) {
                    return strcasecmp($y['label'], $x['label']);
                });
            }
            if ($item->catSortBy == 'position' && $item->catSortOrder == 'desc') {
                usort($categoryArray, function ($x, $y) {
                    return strcasecmp($y['position'], $x['position']);
                });
            }
        }
        return $categoryArray;
    }

    /**
     * @param $subcats
     * @param $item
     * @param bool $childs
     * @param int $level
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setChildCategoryColumn($subcats, $item, $columnCount = 0, $childs = false, $level = 1)
    {
        if (!$childs) {
            $categoryArray = $this->prepareCategoryItems($subcats, $item);
        } else {
            $categoryArray = $subcats;
        }
        if (!$categoryArray) {
            return '';
        }
        $html = '';
        $ulClass = '';
        $countChild = '';
        if ($columnCount !== 0) {
            $ulClass .= 'column'.$columnCount.' child-level-1';
        } else {
            $ulClass .= 'child-level-'.$level;
        }
        $openInNewTabText = '';
        if ($item->getOpenInNewTab()) {
            $openInNewTabText = 'target="_blank"';
        }
        $html .= '<ul class="'.$ulClass.'">';
        foreach ($categoryArray as $cat) {
            $verticalclass = $cat['id'] == $this->getCurrentCat() ? 'active' : '';
            $uniqueClass = 'category-item nav-'.$item->getItemId().'-'.$cat['id'];
            if ($item->getCategoryDisplay()) {
                $countChild = $this->getCategoryCount($cat['id']);
            }
            if ($item->getProductDisplay()) {
                $countChild = $this->getProductCount($cat['id']);
            }
            $liClass = $uniqueClass.' '.$verticalclass;
            $html .= '<li class='.$liClass.'">';
            $html .= '<a href="'.$cat['url'].'" '.$openInNewTabText.'>'.__($cat['label']).$countChild.$this->getCategoryMenuLabelHtml($cat['id']).'</a>';
            if ($item->getProductDisplay()) {
                $html .= $this->getCategoryProducts($cat, $item, $level+1);
            } else {
                //$html .= $this->setChildCategoryColumn($cat['childrens'],$item,0,true,$level+1);
                $html .= $this->setVerticalChildCategoryColumn($cat['childrens'], $item, 0, true, $level+1);
            }$html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }
    /**
     * @param $subcats
     * @param $item
     * @param bool $childs
     * @param int $level
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setAllCatChildCategoryColumn($subcats, $item, $menuId, $columnCount = 0, $childs = false, $level = 1)
    {
        if (!$childs) {
            $categoryArray = $this->prepareCategoryItems($subcats, $item);
        } else {
            $categoryArray = $subcats;
        }

        if (!$categoryArray) {
            return '';
        }
        $html = '';
        $ulClass = '';
        $isLevel2 = false;
        $count = 0;
        $hidden = '';
        $noOfSubCategoryToShow = $this->getNoOfSubCategoryToShow($menuId);
        if ($columnCount !== 0) {
            $ulClass .= 'column'.$columnCount.' child-level-1';
        } else {
            $ulClass .= 'child-level-'.$level;
            if ($level == 2) {
                $isLevel2 = true;
            }
        }
        $html .= '<ul class="'.$ulClass.'">';
        foreach ($categoryArray as $cat) {
            $catIconImgHtml = $this->getCategoryIconImageHtml($cat['id']);
            $countChild = $this->getChildCategoryCount($cat);
            $verticalclass = $cat['id'] == $this->getCurrentCat() ? 'active' : '';
            $uniqueClass = 'category-item nav-'.$item->getItemId().'-'.$cat['id'];
            $liClass = $uniqueClass.' '.$verticalclass;
            if ($isLevel2 && $count == $noOfSubCategoryToShow) {
                $hidden = 'style="display: none"';
            }
            $html .= '<li class="'.$liClass.'" '.$hidden.'>';
            $html .= '<a href="'.$cat['url'].'">'.$catIconImgHtml.__($cat['label']).'<span class="category_count">'.$countChild.'</span>'.$this->getCategoryMenuLabelHtml($cat['id']).'</a>';
            $html .= $this->setAllCatChildCategoryColumn($cat['childrens'], $item, $menuId, 0, true, $level+1);
            $html .= '</li>';
            $count++;
        }
        //if($this->isAllCategoryMegaMenuSelected($menuId)){
        if ($isLevel2 && count($categoryArray) > $noOfSubCategoryToShow) {
            $html .= '<li class="show_less" '.$hidden.'><a href="#">Show Less</a></li><li class="show_more"><a href="#">Show More</a></li>';
        }
        //}
        $html .= '</ul>';
        return $html;
    }

    /**
     * @param $subcats
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function prepareCategoryItems($subcats, $item, $level = 1)
    {
        $leftArray = [];
        $sortArray = "";
        /** @var $subcats \Magento\Catalog\Model\ResourceModel\Category\Collection */
        if ($item->getVerticalCatSortby() && $item->getVerticalCatSortorder()) {
            $sortArray = [
                'sort_by' => $item->getVerticalCatSortby(),
                'sort_order' => $item->getVerticalCatSortorder()
            ];
        }
        foreach ($subcats as $subcat) {
            if (in_array($subcat->getId(), $this->getExcludeCategoryItemId($item))) {
                continue;
            }
            $maxLevel = $item->getVerticalCatLevel() ? (int) $item->getVerticalCatLevel() : 2;
            if ($maxLevel < $level) {
                break;
            }
            $_category = $this->megamenuManagement->getCategoryById($subcat->getId());
            $childrenCats = $this->megamenuManagement->getChildrenCategoriesById($subcat->getId(), $sortArray);
            $group = [
                'id' => $subcat->getId(),
                'label' => $_category->getMdMenuTitle() ? $_category->getMdMenuTitle() : $_category->getName(),
                'url' => $_category->getUrl(),
                'childrens' => $this->prepareCategoryItems($childrenCats, $item, $level+1)
            ];
            $leftArray[] = $group;
        }
        return $leftArray;
    }

    /**
     * @param $item
     * @return array
     */
    public function getExcludeCategoryItemId($item)
    {
        $categories = [];
        $excludeCategory = $item->getVerticalCatExclude();
        if ($excludeCategory) {
            $categories = explode(',', $excludeCategory);
        }
        return $categories;
    }

    /**
     * @param $item
     * @param $subcats
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setVerticalCategoryItem($item, $subcats)
    {
        $leftArray = $this->prepareCategoryItems($subcats, $item);
        $childHtml = '<div class="col-menu-9 vertical-menu-content">';
        $html = '<div class="col-menu-3 vertical-menu-left" style="background:#'.$item->getCategoryVerticalMenuBg().';">';
        $html .= '<ul class="vertical-menu-left-nav">';
        $level = 1;
        foreach ($leftArray as $key => $subcat) {
            $verticalclass = $subcat['id'] == $this->getCurrentCat() ? 'active' : '';
            $addDropdownClass = !empty($childrenCats) ? " dropdown" : "";
            $uniqueClass = 'menu-vertical-items nav-'.$item->getItemId();
            $liClass = $uniqueClass.' '.$verticalclass.' '.$addDropdownClass;
            $datToggle = 'subcat-tab-'.$subcat['id'];
            $html .= '<li class="'.$liClass.'" data-toggle="'.$datToggle.'">';
            $html .= '<a href="'.$subcat['url'].'">'.__($subcat['label']).'</a>';
            $html .= '</li>';
            if ($item->getProductDisplay()) {
                $childHtml .= '<div id="'. $datToggle .'" class="vertical-subcate-content">';
                $childHtml .= $this->getCategoryProducts($subcat, $item, $level+1);
                $childHtml .= '</div>';
            } else {
                $childHtml .= $this->setVerticalRightParentItem($subcat);
            }
        }
        $html .= '</ul>';
        // End Left Column
        $html .= '</div>';
        $childHtml .= '</div>';
        return $html.$childHtml;
    }

    /**
     * @param $childrens
     * @return string
     */
    public function setVerticalRightParentItem($childrens)
    {
        $html = '';
        $columnCountForVerticalMenu = count($childrens['childrens']) >= 3 ? 3 : count($childrens['childrens']);
        $html .= '<div id="subcat-tab-' . $childrens['id'] . '" class="vertical-subcate-content">';
        $html .= '<ul class="menu-vertical-child child-level-3 column' . $columnCountForVerticalMenu . '">';
        foreach ($childrens['childrens'] as $child) {
            $verticalclass = $child['id'] == $this->getCurrentCat() ? 'active' : '';
            $html .= '<li class="' . $verticalclass . '">';
            $html .= '<h4 class="level-3-cat">';
            $html .= '<a href="' . $child['url'] . '">' . $child['label'] . '</a>';
            $html .= '</h4>';
            $html .= $this->setVerticalRightChildItem($child);
            $html .= '</li>';
        }
        $html .= '</ul>';
        $html .= '</div>';
        return $html;
    }

    /**
     * @param $childrens
     * @return string
     */
    public function setVerticalRightChildItem($childrens, $level = 4)
    {
        $html = '';
        if (empty($childrens['childrens'])) {
            return '';
        }
        $html .= '<ul class="menu-vertical-child-item child-level-'.$level.'">';
        foreach ($childrens['childrens'] as $child) {
            $verticalclass = $child['id'] == $this->getCurrentCat() ? 'active' : '';
            $html .= '<li class="' . $verticalclass . '">';
            $html .= '<a href="' . $child['url'] . '">' . $child['label'] . '</a>';
            if (!empty($child['childrens'])) {
                $html .= $this->setVerticalRightChildItem($child, $level+1);
            }
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockObjectHtml($id)
    {
        $blockObject = $this->getLayout()->createBlock('Magento\Cms\Block\Block');
        $blockObject->setBlockId($id);
        return $blockObject->toHtml();
    }

    /**
     * @param $id
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createCmsBlockHtml($id, $title, $class)
    {
        $html = '';
        $headerblock = $this->megamenuManagement->loadCmsBlock($id);
        $html .= '<li class="'.$class.'">';
        if ($title === '1') {
            $html .= '<h2>' . $headerblock->getTitle() . '</h2>';
        }
        $html .= '<ul><li>' . $this->getBlockObjectHtml($id) . '</li>';
        $html .= '</ul></li>';
        return $html;
    }

    /**
     * @return string
     */
    public function menuStyleHtml()
    {
        if (!is_null($this->primaryMenu->getMenuStyle())) {
            if (!empty(trim($this->primaryMenu->getMenuStyle()))) {
                 return '<style>' . $this->primaryMenu->getMenuStyle() . '</style>';
            }
        }
        return '';
    }

    /**
     * @return mixed
     */
    public function animationTime()
    {
        return $this->helper->getConfig('magedelight/general/animation_time');
    }

    /**
     * @return bool
     */
    public function getConfigBurgerStatus()
    {
        if ($this->helper->isEnabled() && $this->helper->isHumbergerMenu()) {
            return true;
        }

        return false;
    }

    /**
     * @param Node $item
     * @return array
     */
    protected function _getMenuItemClasses(Node $item)
    {
        $classes = parent::_getMenuItemClasses($item);

        /* Burger menu for desktop */
        if ($this->getConfigBurgerStatus()) {
            if ($item->getLevel() == 1) {
                if (!empty($this->mdColumnCount) && $this->mdColumnCount != 0) {
                    $classes[] = 'col-'. $this->mdColumnCount;
                }
            }
        }
        return $classes;
    }

    /**
     * @param \Magento\Framework\Data\Tree\Node $child
     * @param string $childLevel
     * @param string $childrenWrapClass
     * @param int $limit
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addSubMenu($child, $childLevel, $childrenWrapClass, $limit)
    {
        /* Burger menu for desktop */
        if (!$this->getConfigBurgerStatus()) {
            return parent::_addSubMenu($child, $childLevel, $childrenWrapClass, $limit);
        }

        $html = '';

        if ($childLevel == 0) {
            $catIdArray = explode('-', $child->getId());
            $this->categoryData = $this->megamenuManagement->getCategoryById(end($catIdArray));
            $getLabel = $this->categoryData->getData('md_label');
            $this->getDescription = $this->categoryData->getData('md_category_editor');
            $color = $this->categoryData->getData('md_label_text_color');
            $backgroundColor = $this->categoryData->getData('md_label_background_color');
            $this->mdColumnCount = $this->categoryData->getData('md_column_count');
            if (isset($getLabel) && $getLabel != '') {
                $html .= '<span class="md-label-text" style="color:'. $color .'!important;background-color:'.
                    $backgroundColor .'!important; ">' .__($getLabel).'</span>';
            }
        }

        if (!$child->hasChildren()) {
            return $html;
        }

        $colStops = [];
        if ($childLevel == 0 && $limit) {
            $colStops = $this->_columnBrake($child->getChildren(), $limit);
        }

        if ($childLevel == 0) {
            $html .= '<ul class="level' . $childLevel . ' ' . $childrenWrapClass . '"><li class="md-submenu-container"><ul class="md-categories">';
            $html .= $this->_getHtml($child, $childrenWrapClass, $limit, $colStops);
            $html .= '</ul><ul class="md-categories-image"><li>' . $this->output->categoryAttribute($this->categoryData, $this->getDescription, 'md_category_editor') . '</li></ul></li></ul>';
        } else {
            $html .= '<ul class="level' . $childLevel . ' ' . $childrenWrapClass . '">';
            $html .= $this->_getHtml($child, $childrenWrapClass, $limit, $colStops);
            $html .= '</ul>';
        }

        return $html;
    }
    /**
     * @return object
     */
    public function getPrimaryMenuObj()
    {
        if ($this->helper->isEnabled()) {
            $_customerSession = $this->customerSession->create();
            if ($_customerSession->isLoggedIn()) {
                $this->primaryMenu = $this->megamenuManagement->getMenuData($_customerSession->getCustomerId())->getMenu();
            } else {
                $this->primaryMenu = $this->megamenuManagement->getMenuData()->getMenu();
            }
        }
        return $this->primaryMenu;
    }
    /**
     * @param $outermostClass
     * @param $childrenWrapClass
     * @param $limit
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getHorizontalMenuHtml($outermostClass, $childrenWrapClass, $limit)
    {
        $this->primaryMenu = $this->getPrimaryMenuObj();
        if ($this->helper->isEnabled() && $this->primaryMenu->getIsActive()) {
            $menuItems = $this->megamenuManagement->loadMenuItems(0, 'ASC');
            if ($this->primaryMenu->getMenuType() == Menu::MEGA_MENU) {
                $html = '';
                foreach ($menuItems as $item) {
                    $childrenWrapClass = "level0 nav-1 first parent main-parent";
                    $html .= $this->setMegamenu($item, $childrenWrapClass);
                }
            } else {
                $parent = 'root';
                $level = 0;
                $html = $this->setPrimaryMenu($menuItems, $level, $parent, $outermostClass);
            }
        } else {
            $html = $this->_getHtml($this->getMenu(), $childrenWrapClass, $limit);
        }
        $transportObject = new \Magento\Framework\DataObject(['html' => $html]);
        $html = $transportObject->getHtml();
        return $html;
    }
    /*
    *@param $item
    *@return bool
    */
    public function isCategoryInactive($item)
    {
        try {
            if ($item->getItemType() == 'category') {
                $category = $this->categoryRepository->get($item->getObjectId());
                if (!$category->getIsActive()) {
                    return true;
                }
            }
        } catch (NoSuchEntityException $e) {
            return false;
        }
        return false;
    }
    /**
     * @return int
     */
    public function isAllCategoryMegaMenuSelected($menuId)
    {
        if ($this->helper->isEnabled()) {
            $allCategoryMenu = $this->getAllCategoryMenuObj($menuId);
            if ($allCategoryMenu->getIsActive() &&
                $allCategoryMenu->getMenuType() == Menu::MEGA_MENU
                && $allCategoryMenu->getMenuDesignType() == self::ALL_CATEGORY_MENU) {
                return true;
            }
        }
        return false;
    }
    public function getNoOfSubCategoryToShow($menuId)
    {
        $allCategoryMenu = $this->getAllCategoryMenuObj($menuId);
        if ($this->isAllCategoryMegaMenuSelected($menuId)) {
            return $allCategoryMenu->getNoOfSubCategoryToShow();
        }
        return 3;
    }
    /**
     * @return object
     */
    public function getAllCategoryMenuObj($menuId)
    {
        if (!$this->helper->isEnabled()) {
            return null;
        }
        if (isset($this->allCategoryMenuData[$menuId])) {
            return $this->allCategoryMenuData[$menuId];
        }
        $this->allCategoryMenuData[$menuId] = $this->megamenuManagement->loadMenuById($menuId);

        return $this->allCategoryMenuData[$menuId];
    }
    public function getAllCategoryMenuTitle($menuId)
    {
        $allCategoryMenuObj = $this->getAllCategoryMenuObj($menuId);
        return $allCategoryMenuObj->getVerticalMenuTitle();
    }
    /**
     * @return String
     */
    public function getAllCategoryNavigationClass($menuId)
    {
        $verticalNavigationClasses = '';
        if ($this->isAllCategoryMegaMenuSelected($menuId)) {
            $verticalNavigationClasses = 'all-category-megamenu-navigation';
        } else {
            $verticalNavigationClasses =  'vertical-navigation';
        }
        return $verticalNavigationClasses;
    }
    public function getShowVerticalMenuOn($menuId)
    {
        $allCategoryMenuObj = $this->getAllCategoryMenuObj($menuId);
        return $allCategoryMenuObj->getShowVerticalMenuOn();
    }
    public function getCategoryIconImageHtml($categoryId)
    {
        $category = $this->categoryRepository->get($categoryId);
        $catImageHtml = '';
        if ($_imgUrl = $this->getCategoryIconImage()->getUrl($category)) {
            $catImageHtml = '<span class="category-icon-image"><img src="'
                . $this->escapeUrl($_imgUrl)
                . '" alt="'
                . $this->escapeHtmlAttr($category->getName())
                . '" title="'
                . $this->escapeHtmlAttr($category->getName())
                . '" /></span>';
        }
        return $catImageHtml;
    }

    public function getChildCategoryCount($cat)
    {
        $categoryId = $cat['id'];
        $category = $this->categoryRepository->get($categoryId);
        $childCatCount = '';
        if ($countChild = $category->getChildrenCount()) {
            $childCatCount = ' ('.$countChild.')';
        }
        return $childCatCount;
    }

    public function getAllCategoryMenuItems()
    {
        $allCategoryMenuItemCollection = $this->megamenuManagement->loadAllMegaMenus();
        $allCategoryMenuItemCollection = $allCategoryMenuItemCollection->addFieldToFilter('menu_design_type', self::ALL_CATEGORY_MENU);
        return $allCategoryMenuItemCollection;
    }

    public function isAllCategoryMenuCreated()
    {
        if ($this->getAllCategoryMenuItems() && count($this->getAllCategoryMenuItems()) > 0) {
            return true;
        }
        return false;
    }
    /**
     * @param \Magento\Framework\Data\Tree\Node $child
     * @param string $childLevel
     * @param string $childrenWrapClass
     * @param int $limit
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCategoryMenuLabelHtml($categoryId)
    {
        $html = '';
        $this->categoryData = $this->megamenuManagement->getCategoryById($categoryId);
        $getLabel = $this->categoryData->getData('md_label');
        $this->getDescription = $this->categoryData->getData('md_category_editor');
        $color = $this->categoryData->getData('md_label_text_color');
        $backgroundColor = $this->categoryData->getData('md_label_background_color');
        $labelShape = $this->categoryData->getData('md_label_shape') ? $this->categoryData->getData('md_label_shape') : '' ;
        if (isset($getLabel) && $getLabel != '') {
            $html .= '<span class="md-label-text '.$labelShape.'" style="color:'. $color .'!important;background-color:'.
            $backgroundColor .'!important; ">' .__($getLabel).'</span>';
        }
        return $html;
    }
    public function isAllCategoryMegamenu($menuId)
    {
        $menu = $this->megamenuManagement->loadMenuById($menuId);
        if ($menu->getMenuType() == Menu::MEGA_MENU) {
            return true;
        }
        return false;
    }
    /**
     * @return int|null
     */
    public function isPrimaryMenuSelected()
    {
        $primaryMenu = $this->helper->isPrimaryMenuSelected();
        if ($primaryMenu === self::PRIMARY_NONE) {
            return null;
        }

        $menu = $this->megamenuManagement->loadMenuById($primaryMenu);
        if (!$menu->getIsActive()) {
            return null;
        }

        return $primaryMenu;
    }

    public function getDisplayPosition($menuId)
    {
        $allCategoryMenuObj = $this->getAllCategoryMenuObj($menuId);
        return $allCategoryMenuObj->getDisplayPosition();
    }
    public function getDisplayOverlay($menuId)
    {
        $allCategoryMenuObj = $this->getAllCategoryMenuObj($menuId);
        return $allCategoryMenuObj->getDisplayOverlay();
    }
    public function getPrimaryMenuDisplayOverlay()
    {
        if ($this->primaryMenu->getDisplayOverlay()) {
            return true;
        }
        return false;
    }
    public function getCategoryProducts($cat, $item, $level)
    {
        $html = '';
        $categoryId = $cat['id'];
        $categoryUrl = $cat['url'];
        $sortBy = $item->getVerticalCatSortby();
        $sortOrder = $item->getVerticalCatSortorder();
        $ulClass = 'child-level-'.$level;
        $showMoreStatus = $this->primaryMenu->getShowViewMore();
        $noOfSubCategoryToShow = $this->primaryMenu->getNoOfSubCategoryToShow();
        $excludeProductIds = explode(',', $item->getVerticalCatExclude() ?? '');
        $category = $this->categoryFactory->create()->load($categoryId);
        $categoryProducts = $category->getProductCollection()
                                ->addAttributeToSelect('name')
                                ->addAttributeToSelect('url_key')
                                ->addAttributeToSelect('md_menu_label')
                                ->addAttributeToSelect('md_menu_label_shape')
                                ->addAttributeToSelect('md_label_text_color')
                                ->addAttributeToSelect('md_label_background_color')
                                ->setOrder($sortBy, $sortOrder)
                                ->addAttributeToFilter('entity_id', ['nin' => $excludeProductIds])
                                ->setPageSize($noOfSubCategoryToShow);
        if ($categoryProducts->getSize() < 1) {
            return '';
        }
        $html .= '<ul class="'.$ulClass.'">';
        foreach ($categoryProducts as $product) {
            $html .= '<li class="product-item"><a href="'.$product->getProductUrl().'" title="Explore '.$product->getName().'">';
            $html .= __($product->getName());
            if ($product->getMdMenuLabel()) {
                $inlineStyle = 'style="color: '.$product->getMdLabelTextColor().'; background-color:'.$product->getMdLabelBackgroundColor().'"';
                $html .= '<span class="md-label-text '.$product->getMdMenuLabelShape().'" '.$inlineStyle.'>'.$product->getMdMenuLabel().'</span>';
            }
            $html .= '</a></li>';
        }
        /*if(count($categoryProducts) > $noOfSubCategoryToShow){*/
        if ($showMoreStatus) {
            $html .= '<li class="view_more"><a href="'.$categoryUrl.'">View More</a></li>';
        }
        /*}*/
        $html .= '</ul>';
        return $html;
    }
    public function getAmazonMenus()
    {
        $megaMenuCollection = $this->megamenuManagement->loadAllMegaMenus();
        $megaMenuCollection = $megaMenuCollection->addFieldToFilter('menu_design_type', self::AMAZON_MENU);
        return $megaMenuCollection;
    }
    public function isAmazonMenuCreated()
    {
        if ($this->getAmazonMenus() && count($this->getAmazonMenus()) > 0) {
            return true;
        }
        return false;
    }
    public function setVerticalChildCategoryColumn($subcats, $item, $columnCount = 0, $childs = false, $level = 1)
    {
        $html = '';
        $ulClass = 'child-level-'.$level;
        $html .= '<div class="md-hv-right">';
        $html .= '<ul class="'.$ulClass.'">';
        foreach ($subcats as $subcat) {
            $html .= '<li class="product-item"><a href="'.$subcat['url'].'" title="Explore '.$subcat['label'].'">';
            $html .= __($subcat['label']);
            $html .= $this->getCategoryMenuLabelHtml($subcat['id']);
            $html .= '</a></li>';
        }
        $html .= '</ul>';
        $html .= '</div>';
        return $html;
    }
    public function getCategoryCount($catId)
    {
        $showCatCount = $this->primaryMenu->getShowCategoryCount();
        $countChild = '';
        if ($showCatCount) {
            $categoryLoad = $this->megamenuManagement->getCategoryById($catId);
            $countChild = '<span class="category-count"> ('.$categoryLoad->getChildrenCount().')</span>';
        }
        return $countChild;
    }
    public function getProductCount($catId)
    {
        $showCatCount = $this->primaryMenu->getShowCategoryCount();
        $countChild = '';
        if ($showCatCount) {
            $categoryLoad = $this->megamenuManagement->getCategoryById($catId);
            $countChild = '<span class="category-count"> ('.$categoryLoad->getProductCollection()->count().')</span>';
        }
        return $countChild;
    }
}
