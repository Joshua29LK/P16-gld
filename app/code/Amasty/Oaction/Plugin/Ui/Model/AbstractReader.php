<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Plugin\Ui\Model;

use Amasty\Oaction\Helper\Data;
use Amasty\Oaction\Model\Action\ActionChecker;
use Amasty\Oaction\Model\Action\ActionModifierProvider;
use Amasty\Oaction\Model\Source\State;

class AbstractReader
{
    public const UI_COMPONENT = 'Amasty_Oaction/js/grid/tree-massactions';

    public const ACTION_STATUS = 'amasty_oaction_status';

    public const ACTION_STATUS_NOTIFY = 'amasty_oaction_statusnotify';

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var State
     */
    private $orderStatus;

    /**
     * @var ActionChecker
     */
    private $actionChecker;

    /**
     * @var ActionModifierProvider
     */
    private $actionModifierProvider;

    public function __construct(
        Data $helper,
        State $orderStatus,
        ActionChecker $actionChecker,
        ActionModifierProvider $actionModifierProvider
    ) {
        $this->helper = $helper;
        $this->orderStatus = $orderStatus;
        $this->actionChecker = $actionChecker;
        $this->actionModifierProvider = $actionModifierProvider;
    }

    /**
     * @param array $result
     *
     * @return array
     */
    protected function updateMassactions(array $result): array
    {
        if (isset($result['children']['listing_top']['children']['listing_massaction']['children'])
            && isset($result['children']['sales_order_grid_data_source'])
        ) {
            $children = &$result['children']['listing_top']['children']['listing_massaction']['children'];

            foreach ($children as $item) {
                $actionName = $item['attributes']['name'];

                if (!$this->actionChecker->isActionAvailable($actionName)) {
                    unset($children[$actionName]);
                    continue;
                }

                if (in_array($actionName, [self::ACTION_STATUS, self::ACTION_STATUS_NOTIFY])) {
                    $children[$actionName] = $this->addStatusValues(
                        $children[$actionName]
                    );
                }

                if ($modifier = $this->actionModifierProvider->get($actionName)) {
                    $modifier->modify($children);
                }
            }

            // phpcs:ignore: Generic.Files.LineLength.TooLong
            $component = &$result['children']['listing_top']['children']['listing_massaction']['arguments']['data']['item']['config']['item']['component']['value'];

            if ($component !== self::UI_COMPONENT) {
                $component = self::UI_COMPONENT;
            }
        }

        return $result;
    }

    /**
     * @param array $item
     *
     * @return array
     */
    private function addStatusValues(array $item): array
    {
        $childItem = [];
        $i = 0;
        $excludedStatuses = explode(',', (string)$this->helper->getModuleConfig('status/exclude_statuses'));
        $statuses = $this->orderStatus->toOptionArray();
        array_unshift($statuses, [
            'value' => '',
            'label' => __('Magento default')->render()
        ]);

        foreach ($statuses as $status) {
            if (!in_array($status['value'], $excludedStatuses) || $status['value'] == '') {
                $childItem[] = [
                    "name" => (string)$i++,
                    "xsi:type" => "array",
                    "item" => [
                        "label" => [
                            "name" => "label",
                            "xsi:type" => "string",
                            "value" => $status['label']
                        ],
                        "fieldvalue" => [
                            "name" => "fieldvalue",
                            "xsi:type" => "string",
                            "value" => $status['value']
                        ],
                    ]
                ];
            }
        }

        $item['arguments']['actions']['item'][0]['item']['child'] = [
            "name" => "child",
            "xsi:type" => "array",
            'item' => $childItem
        ];

        return $item;
    }
}
