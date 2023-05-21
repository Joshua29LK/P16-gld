<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

namespace Amasty\Flags\Plugin\Ui\Component\Listing;

use Amasty\Flags\Model\Column;
use Amasty\Flags\Model\Flag;
use Amasty\Flags\Model\ResourceModel\Column\CollectionFactory as ColumnCollectionFactory;
use Amasty\Flags\Model\ResourceModel\Flag\CollectionFactory as FlagCollectionFactory;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class Columns
{
    public const BASE_SORT_ORDER = 2;

    /**
     * @var ColumnCollectionFactory
     */
    private $columnCollectionFactory;
    /**
     * @var UiComponentFactory
     */
    private $componentFactory;
    /**
     * @var FlagCollectionFactory
     */
    private $flagCollectionFactory;
    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    public function __construct(
        ColumnCollectionFactory $columnCollectionFactory,
        FlagCollectionFactory $flagCollectionFactory,
        UiComponentFactory $componentFactory,
        AuthorizationInterface $authorization
    ) {
        $this->columnCollectionFactory = $columnCollectionFactory;
        $this->componentFactory = $componentFactory;
        $this->flagCollectionFactory = $flagCollectionFactory;
        $this->authorization = $authorization;
    }

    /**
     * @param \Magento\Ui\Component\Listing\Columns $subject
     * @param \Closure                              $proceed
     *
     * @return mixed
     */
    public function aroundPrepare(\Magento\Ui\Component\Listing\Columns $subject, \Closure $proceed)
    {
        if ($subject->getName() != 'sales_order_columns') {
            return $proceed();
        }

        $isAssignAllowed = $this->authorization->isAllowed('Amasty_Flags::assign_flags');

        $columnSortOrder = self::BASE_SORT_ORDER;
        $components = $subject->getChildComponents();
        $columns = $this->columnCollectionFactory->create();
        /** @var \Amasty\Flags\Model\ResourceModel\Flag\Collection $flags */
        $flags = $this->flagCollectionFactory->create();
        /** @var Column $column */
        foreach ($columns as $column) {
            $columnCode = 'amflags_column_' . $column->getId();
            if (!isset($components[$columnCode])) {
                $options = [];

                foreach ($column->getAppliedFlagIds() as $flagId) {
                    /** @var Flag $flag */
                    $flag = $flags->getItemById($flagId);

                    if ($flag) {
                        $options []= [
                            'label' => __($flag->getName()),
                            'value' => $flag->getId()
                        ];
                    }
                }

                $config = [
                    'label' => __($column->getName()),
                    'sortOrder' => $columnSortOrder++,
                    'add_field' => false,
                    'visible' => true,
                    'dataType' => 'select',
                    'filter' => 'select',
                    'component' => 'Amasty_Flags/js/grid/columns/flag',
                    'columnId' => (int)$column->getId(),
                    'options' => $options,
                    'fieldClass' => 'amasty_flag_cell',
                    'isAssignAllowed' => $isAssignAllowed
                ];

                $arguments = [
                    'data' => [
                        'config' => $config,
                    ],
                    'context' => $subject->getContext(),
                ];

                /** @var \Magento\Ui\Component\Listing\Columns\Column $column */
                $column = $this->componentFactory->create($columnCode, 'column', $arguments);
                $column->prepare();
                $subject->addComponent($columnCode, $column);
            }
        }

        return $proceed();
    }
}
