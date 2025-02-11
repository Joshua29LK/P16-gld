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
namespace Magedelight\Megamenu\Model\Menu\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class MenuType
 *
 * @package Magedelight\Megamenu\Model\Menu\Source
 */
class MenuType implements OptionSourceInterface
{
    /**
     * @var \Magedelight\Megamenu\Model\Menu
     */
    protected $megamenuMenu;

    /**
     * Constructor
     *
     * @param \Magedelight\Megamenu\Model\Menu $megamenuMenu
     */
    public function __construct(
        \Magedelight\Megamenu\Model\Menu $megamenuMenu
    ) {
        $this->megamenuMenu = $megamenuMenu;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->megamenuMenu->getAvailableTypes();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
