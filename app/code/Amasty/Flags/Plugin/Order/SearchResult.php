<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

namespace Amasty\Flags\Plugin\Order;

use Amasty\Flags\Model\Flag;
use Amasty\Flags\Model\ResourceModel\Column\CollectionFactory as ColumnCollectionFactory;
use Amasty\Flags\Model\ResourceModel\Flag\CollectionFactory as FlagCollectionFactory;
use Magento\Framework\DB\Select;

class SearchResult
{
    /**
     * @var bool
     */
    protected $locked = false; // prevent recursive calls in "load" function

    /**
     * @var ColumnCollectionFactory
     */
    private $columnCollectionFactory;
    /**
     * @var FlagCollectionFactory
     */
    private $flagCollectionFactory;
    /**
     * @var Flag
     */
    private $flagSingleton;

    public function __construct(
        ColumnCollectionFactory $columnCollectionFactory,
        FlagCollectionFactory $flagCollectionFactory,
        Flag $flagSingleton
    ) {
        $this->columnCollectionFactory = $columnCollectionFactory;
        $this->flagCollectionFactory = $flagCollectionFactory;
        $this->flagSingleton = $flagSingleton;
    }

    /**
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult $collection
     * @param Select|null                                                           $select
     *
     * @return Select|null
     */
    public function afterGetSelect(
        \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult $collection,
        $select
    ) {
        if ((string)$select == ""
            || !($collection->getResource() instanceof \Magento\Sales\Model\ResourceModel\Order)
        ) {
            return $select;
        }

        /** @var \Amasty\Flags\Model\ResourceModel\Column\Collection $columns */
        $columns = $this->columnCollectionFactory->create();
        $orderFlagTable = $collection->getTable('amasty_flags_order_flag');

        foreach ($columns as $column) {
            $columnCode = 'amflags_column_' . (int)$column->getId();

            if (!array_key_exists($columnCode, $select->getPart('from'))) {
                $select
                    ->joinLeft(
                        [$columnCode => $orderFlagTable],
                        "main_table.entity_id = $columnCode.order_id AND $columnCode.column_id = "
                        . (int)$column->getId(),
                        [
                            $columnCode => "$columnCode.flag_id",
                            $columnCode . '_note' => "$columnCode.note"
                        ]
                    );
            }
        }

        return $select;
    }

    public function afterLoad(\Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult $subject, $result)
    {
        if ($this->locked || !($subject->getResource() instanceof \Magento\Sales\Model\ResourceModel\Order)) {
            return $result;
        }

        $this->locked = true;

        /** @var \Amasty\Flags\Model\ResourceModel\Column\Collection $columns */
        $columns = $this->columnCollectionFactory->create();

        /** @var \Amasty\Flags\Model\ResourceModel\Flag\Collection $flags */
        $flags = $this->flagCollectionFactory->create();

        foreach ($subject as $item) {
            if ($item->hasData('amflags_data_processed')) {
                break;
            }

            foreach ($columns as $column) {
                $columnCode = 'amflags_column_' . (int)$column->getId();

                $flagId = $item->getData($columnCode);
                if (!$flagId) {
                    $item
                        ->setData($columnCode . '_src', $this->flagSingleton->getImagePlaceholderUrl())
                        ->setData($columnCode . '_alt', $column->getComment() ?: __('No flag'))
                    ;

                    continue;
                }
                /** @var Flag $flag */
                $flag = $flags->getItemById($flagId);

                if ($flag) {
                    $note = $item->getData($columnCode . '_note') ?: $flag->getNote() ?: $column->getComment();

                    $item
                        ->setData($columnCode . '_src', $flag->getImageUrl())
                        ->setData($columnCode . '_alt', $note)
                    ;
                }
            }

            $item->setData('amflags_data_processed', true);
        }

        $this->locked = false;
        return $result;
    }
}
