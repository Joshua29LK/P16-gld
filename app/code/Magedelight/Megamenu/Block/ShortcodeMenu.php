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
 * Class Topmenu
 *
 * @package Magedelight\Megamenu\Block
 */
class ShortcodeMenu extends Topmenu
{
    /**
     * Get top menu html
     *
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @return string
     */
    public function isStickyEnable()
    {
        $this->primaryMenuId = $this->getMenuid();
        $this->primaryMenu = $this->megamenuManagement->loadMenuById($this->primaryMenuId);
        return $this->primaryMenu->getIsSticky();
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
        $html = '';
        $this->primaryMenuId = $this->getMenuid();
        $this->primaryMenu = $this->megamenuManagement->loadMenuById($this->primaryMenuId);

        if ($this->helper->isEnabled() && $this->primaryMenu->getIsActive() == 1) {
            $menuItems = $this->megamenuManagement->loadMenuItems(0, 'ASC', $this->primaryMenuId);
            foreach ($menuItems as $item) {
                $childrenWrapClass = "level0 nav-1 first parent main-parent";
                $html .= $this->setMegamenu($item, $childrenWrapClass);
            }
        }

        $transportObject = new \Magento\Framework\DataObject(['html' => $html]);
        $this->_eventManager->dispatch(
            'shortcode_block_html_topmenu_gethtml_after',
            ['menu' => $this->primaryMenuId, 'transportObject' => $transportObject]
        );
        $html = $transportObject->getHtml();
        return $html;
    }

    /**
     * @return int|null
     */
    public function getCacheLifetime()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getMenuDesign()
    {
        return $this->primaryMenu->getMenuDesignType();
    }
}
