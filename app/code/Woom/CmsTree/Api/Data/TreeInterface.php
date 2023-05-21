<?php

namespace Woom\CmsTree\Api\Data;

/**
 * @api
 */
interface TreeInterface
{
    const STORE_TABLE = 'store';

    const CMS_PAGE_TABLE = 'cms_page';

    const CMS_PAGE_STORE_TABLE = 'cms_page_store';

    const SAMBOLEK_TREE_CMS_PAGE_TABLE = 'sambolek_cms_page_tree';

    const TREE_CMS_PAGE_TABLE = 'woom_cms_page_tree';

    const TREE_CMS_PAGE_STORE_TABLE = 'woom_cms_page_tree_store';

    const TREE_ROOT_ID = '1';

    const ROOT_CMS_TREE_ID_COLUMN = 'root_cms_tree_id';
    
    const TREE_ID = 'tree_id';

    const PARENT_TREE_ID = 'parent_tree_id';

    const PAGE_ID = 'page_id';

    const IDENTIFIER = 'identifier';

    const REQUEST_URL = 'request_url';

    const TITLE = 'title';

    const PATH = 'path';

    const POSITION = 'position';

    const LEVEL = 'level';

    const CHILDREN_COUNT = 'children_count';

    const MENU_LABEL = 'menu_label';

    const IS_IN_MENU = 'is_in_menu';

    const MENU_ADD_TYPE = 'menu_add_type';

    const MENU_ADD_CATEGORY_ID = 'menu_add_category_id';
    
    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get parent ID
     *
     * @return int|null
     */
    public function getParentId();

    /**
     * Get page ID
     *
     * @return int
     */
    public function getPageId();

    /**
     * Get identifier
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Get request url
     *
     * @return string|null
     */
    public function getRequestUrl();

    /**
     * Get path
     *
     * @return string|null
     */
    public function getPath();

    /**
     * Get position
     *
     * @return int|null
     */
    public function getPosition();

    /**
     * Get level
     *
     * @return int|null
     */
    public function getLevel();

    /**
     * Get children count
     *
     * @return int|null
     */
    public function getChildrenCount();

    /**
     * Get flag if included in menu
     *
     * @return bool
     */
    public function isInMenu();

    /**
     * Get menu add type
     *
     * @return int|null
     */
    public function getMenuAddType();

    /**
     * Get menu add category id
     *
     * @return int|null
     */
    public function getMenuAddCategoryId();

    /**
     * Get menu label
     *
     * @return string
     */
    public function getMenuLabel();

    /**
     * Set ID
     *
     * @param int $identifier
     * @return \Woom\CmsTree\Api\Data\TreeInterface
     */
    public function setId($identifier);

    /**
     * Set parent ID
     *
     * @param int $parentId
     * @return \Woom\CmsTree\Api\Data\TreeInterface
     */
    public function setParentId($parentId);

    /**
     * Set page ID
     *
     * @param int $pageId
     * @return \Woom\CmsTree\Api\Data\TreeInterface
     */
    public function setPageId($pageId);

    /**
     * Set identifier
     *
     * @param string $identifier
     * @return \Woom\CmsTree\Api\Data\TreeInterface
     */
    public function setIdentifier($identifier);

    /**
     * Set request url
     *
     * @param string $requestUrl
     * @return \Woom\CmsTree\Api\Data\TreeInterface
     */
    public function setRequestUrl($requestUrl);

    /**
     * Set path
     *
     * @param string $path
     * @return \Woom\CmsTree\Api\Data\TreeInterface
     */
    public function setPath($path);

    /**
     * Set title
     *
     * @param string $title
     * @return \Woom\CmsTree\Api\Data\TreeInterface
     */
    public function setTitle($title);

    /**
     * Set position
     *
     * @param string $position
     * @return \Woom\CmsTree\Api\Data\TreeInterface
     */
    public function setPosition($position);

    /**
     * Set level
     *
     * @param int $level
     * @return \Woom\CmsTree\Api\Data\TreeInterface
     */
    public function setLevel($level);

    /**
     * Set children count
     *
     * @param int $childrenCount
     * @return \Woom\CmsTree\Api\Data\TreeInterface
     */
    public function setChildrenCount($childrenCount);

    /**
     * Set include in menu flag
     *
     * @param bool $includeInMenu
     * @return \Woom\CmsTree\Api\Data\TreeInterface
     */
    public function setIsIsMenu($includeInMenu);

    /**
     * Set menu add type
     *
     * @param int $menuAddType
     * @return \Woom\CmsTree\Api\Data\TreeInterface
     */
    public function setMenuAddType($menuAddType);

    /**
     * Set menu add category id
     *
     * @param int $menuAddCategoryId
     * @return \Woom\CmsTree\Api\Data\TreeInterface
     */
    public function setMenuAddCategoryId($menuAddCategoryId);

    /**
     * Set menu label
     *
     * @param string $menuLabel
     * @return \Woom\CmsTree\Api\Data\TreeInterface
     */
    public function setMenuLabel($menuLabel);
}
