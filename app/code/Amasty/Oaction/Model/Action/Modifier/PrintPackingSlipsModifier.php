<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Model\Action\Modifier;

use Amasty\Oaction\Model\Action\ActionChecker;

class PrintPackingSlipsModifier implements ActionModifierInterface
{
    public const ACTION_PRINT_PACKING_SLIPS = 'amasty_oaction_printpackingslips';
    public const MAGENTO_ACTION_PRINT_PACKING_SLIPS = 'pdfshipments_order';

    /**
     * @var ActionChecker
     */
    private $actionChecker;

    public function __construct(ActionChecker $actionChecker)
    {
        $this->actionChecker = $actionChecker;
    }

    public function modify(array &$item): void
    {
        if (isset($item[self::MAGENTO_ACTION_PRINT_PACKING_SLIPS])
            && $this->actionChecker->isActionAvailable(self::ACTION_PRINT_PACKING_SLIPS)
        ) {
            $configItem = &$item[self::MAGENTO_ACTION_PRINT_PACKING_SLIPS]['arguments']['data']
                ['item']['config']['item'];
            $configItem['type']['value'] = self::ACTION_PRINT_PACKING_SLIPS;
            $configItem['url']['path'] = 'amasty_oaction/massaction/index/type/printpackingslips';
            $configItem['download'] = [
                'name' => 'download',
                'xsi:type' => 'boolean',
                'value' => 'true'
            ];
        }
    }
}
