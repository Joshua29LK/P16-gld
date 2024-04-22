<?php

/**
 * MageDelight
 * Copyright (C) 2023 Magedelight <info@magedelight.com>
 *
 * @category MageDelight
 * @package Magedelight_Megamenu
 * @copyright Copyright (c) 2023 Magedelight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Megamenu\Block;

/**
 * Class ShortcodeVertical
 *
 * @package Magedelight\Megamenu\Block
 */
class ShortcodeVertical extends Topmenu
{
    public function getAllCategoryMenuItems()
    {
        $menuId = $this->getMenuid();
        $getMenuById = $this->megamenuManagement->loadAllMegaMenus();
        $allCategoryMenuItemCollection = $getMenuById->addFieldToFilter('main_table.menu_id', $menuId)
            ->addFieldToFilter('menu_design_type', self::ALL_CATEGORY_MENU);

        return $allCategoryMenuItemCollection;
    }
}
