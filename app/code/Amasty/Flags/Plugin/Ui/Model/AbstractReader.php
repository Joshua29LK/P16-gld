<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Flags\Plugin\Ui\Model;

use Amasty\Flags\Model\Column;
use Amasty\Flags\Model\Flag;
use Amasty\Flags\Model\ResourceModel\Column\CollectionFactory as ColumnCollectionFactory;
use Amasty\Flags\Model\ResourceModel\Flag\CollectionFactory as FlagCollectionFactory;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class AbstractReader
{
    /**
     * @var ColumnCollectionFactory
     */
    private $columnCollectionFactory;

    /**
     * @var FlagCollectionFactory
     */
    private $flagCollectionFactory;

    /**
     * @var UrlInterface
     */
    private $url;

    public function __construct(
        ColumnCollectionFactory $columnCollectionFactory,
        FlagCollectionFactory $flagCollectionFactory,
        UrlInterface $url
    ) {
        $this->columnCollectionFactory = $columnCollectionFactory;
        $this->flagCollectionFactory = $flagCollectionFactory;
        $this->url = $url;
    }

    /**
     * @param array $result
     *
     * @return array
     */
    protected function addMassactions(array $result): array
    {
        if (isset($result['children']['listing_top']['children']['listing_massaction']['children'])
            && isset($result['children']['sales_order_grid_data_source'])
        ) {
            /** @var \Amasty\Flags\Model\ResourceModel\Column\Collection $columns */
            $columns = $this->columnCollectionFactory->create();
            /** @var \Amasty\Flags\Model\ResourceModel\Flag\Collection $columns */
            $flags = $this->flagCollectionFactory->create();
            $children = &$result['children']['listing_top']['children']['listing_massaction']['children'];

            /** @var Column $column */
            foreach ($columns as $column) {
                $actionKey = 'amflags_assign_' . $column->getId();

                if (!array_key_exists($actionKey, $children)) {
                    $applicableFlags = [];

                    foreach ($column->getAppliedFlagIds() as $flagId) {
                        $flag = $flags->getItemById($flagId);

                        if ($flag) {
                            $applicableFlags[] = $flag;
                        }
                    }

                    if (!empty($applicableFlags)) {
                        $children[$actionKey] = $this->addAssignMenu($column, $applicableFlags);
                    }
                }
            }

            if (!isset($children['amflags_unset'])) {
                $children['amflags_unset'] = $this->addUnassignMenu($columns);
            }
        }

        return $result;
    }

    /**
     * @param Column $column
     * @param array  $applicableFlags
     *
     * @return array
     */
    private function addAssignMenu(Column $column, array $applicableFlags): array
    {
        $actions = [];

        /** @var Flag $flag */
        foreach ($applicableFlags as $index => $flag) {
            $actionUrl = $this->url->getUrl(
                'amasty_flags/flagAssign/massAssign',
                ['column' => $column->getId(), 'flag' => $flag->getId()]
            );

            $actions[] = $this->addAction(
                "amflags_assign_{$column->getId()}_{$flag->getId()}",
                __($flag->getName())->render(),
                $actionUrl,
                __('Assign the "%1" flag to the selected orders?', $flag->getName())->render()
            );
        }

        $result = $this->addMenuItem(
            'amflags_assign_' . $column->getId(),
            __('Assign Flags To "%1" Column', $column->getName())->render(),
            $actions
        );

        return $result;
    }

    /**
     * @param AbstractCollection $columns
     *
     * @return array
     */
    private function addUnassignMenu(AbstractCollection $columns): array
    {
        $actions = [];

        $actions[] = $this->addAction(
            'amflags_unassign_all',
            __('All')->render(),
            $this->url->getUrl('amasty_flags/flagAssign/massUnassign'),
            __('Unassign all the flags for the selected orders?')->render()
        );

        /** @var Column $column */
        foreach ($columns as $column) {
            $actions[] = $this->addAction(
                "amflags_unassign_{$column->getId()}",
                __('For Column "%1"', $column->getName())->render(),
                $this->url->getUrl('amasty_flags/flagAssign/massUnassign', ['column' => $column->getId()]),
                __('Unassign all the flags from "%1" column for the selected orders?', $column->getName())->render()
            );
        }

        $result = $this->addMenuItem(
            'amflags_unassign',
            __('Unassign Flags')->render(),
            $actions
        );

        return $result;
    }

    /**
     * @param string $code
     * @param string $title
     * @param array  $actions
     *
     * @return array
     */
    private function addMenuItem(string $code, string $title, array $actions): array
    {
        return [
            'arguments' => [
                'data' => [
                    'name' => 'data',
                    'xsi:type' => 'array',
                    'item' => [
                        'config' => [
                            'name' => 'config',
                            'xsi:type' => 'array',
                            'item' => [
                                'type' => [
                                    'name' => 'type',
                                    'xsi:type' => 'string',
                                    'value' => $code
                                ],
                                'label' => [
                                    'name' => 'label',
                                    'xsi:type' => 'string',
                                    'translate' => 'true',
                                    'value' => $title
                                ],
                            ]
                        ]
                    ]
                ],
                'actions' => [
                    'name' => 'actions',
                    'xsi:type' => 'array',
                    'item' => $actions
                ]
            ],
            'attributes' => [
                'class' => \Magento\Ui\Component\Action::class,
                'name' => $code
            ],
            'children' => []
        ];
    }

    /**
     * @param string  $code
     * @param string  $title
     * @param string $action
     * @param string  $confirmation
     *
     * @return array
     */
    private function addAction(string $code, string $title, string $action, string $confirmation): array
    {
        return [
            'name' => $code,
            'xsi:type' => 'array',
            'item' => [
                'label' => [
                    'name' => 'label',
                    'xsi:type' => 'string',
                    'translate' => 'true',
                    'value' => $title
                ],
                'url' => [
                    'name' => 'url',
                    'xsi:type' => 'url',
                    'path' => $action
                ],
                'type' => [
                    'name' => 'type',
                    'xsi:type' => 'string',
                    'value' => $code
                ],
                'confirm' => [
                    'name' => 'confirm',
                    'xsi:type' => 'array',
                    'item' => [
                        'title' => [
                            'name' => 'title',
                            'xsi:type' => 'string',
                            'translate' => 'true',
                            'value' => __('Please Confirm')->render()
                        ],
                        'message' => [
                            'name' => 'message',
                            'xsi:type' => 'string',
                            'translate' => 'true',
                            'value' => $confirmation
                        ]
                    ]
                ],
            ]
        ];
    }
}
