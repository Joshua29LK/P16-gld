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
namespace Magedelight\Megamenu\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;

/**
 * Class MenuItems
 *
 * @package Magedelight\Megamenu\Model\ResourceModel
 */
class MenuItems extends AbstractDb
{
    
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('megamenu_menu_items', 'item_id');
    }
}
