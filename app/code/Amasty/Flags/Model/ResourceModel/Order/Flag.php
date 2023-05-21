<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/
namespace Amasty\Flags\Model\ResourceModel\Order;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Flag extends AbstractDb
{
    /**
     * @var Flag\CollectionFactory
     */
    private $orderFlagCollectionFactory;
    /**
     * @var \Amasty\Flags\Model\Order\FlagFactory
     */
    private $flagFactory;

    public function __construct(
        Context $context,
        \Amasty\Flags\Model\ResourceModel\Order\Flag\CollectionFactory $orderFlagCollectionFactory,
        \Amasty\Flags\Model\Order\FlagFactory $flagFactory,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->orderFlagCollectionFactory = $orderFlagCollectionFactory;
        $this->flagFactory = $flagFactory;
    }

    protected function _construct()
    {
        $this->_init('amasty_flags_order_flag', 'id');
    }

    public function assign($orderId, $columnId, $flagId = null, $note = null)
    {
        if (!$flagId) {
            $this->unassign($orderId, $columnId);
            return;
        }

        /** @var \Amasty\Flags\Model\ResourceModel\Order\Flag\Collection $orderFlags */
        $orderFlags = $this->orderFlagCollectionFactory->create();

        $orderFlags
            ->addFieldToFilter('column_id', $columnId)
            ->addFieldToFilter('order_id', $orderId);

        /** @var \Amasty\Flags\Model\Order\Flag $orderFlag */
        $orderFlag = $orderFlags->getFirstItem();

        $orderFlag->addData([
            'order_id' => $orderId,
            'flag_id' => $flagId,
            'column_id' => $columnId,
            'note' => $note
        ]);

        $this->save($orderFlag);
    }

    public function unassign($orderId, $columnId = null)
    {
        $conditions = [
            'order_id = ?' => $orderId
        ];

        if ($columnId) {
            $conditions['column_id = ?'] = $columnId;
        }

        $this->getConnection()->delete(
            $this->getMainTable(),
            $conditions
        );
    }

    public function massAssign(array $orderIds, $columnId, $flagId)
    {
        $orderKeys = array_flip($orderIds);

        /** @var \Amasty\Flags\Model\ResourceModel\Order\Flag\Collection $orderFlags */
        $orderFlags = $this->orderFlagCollectionFactory->create();

        $orderFlags->addFieldToFilter('column_id', $columnId);

        // do not filter by orderIds because it may exceed query max length

        /** @var \Amasty\Flags\Model\Order\Flag $orderFlag */
        foreach ($orderFlags as $orderFlag) {
            $orderId = $orderFlag->getOrderId();

            if (isset($orderKeys[$orderId])) {
                if ($orderFlag->getFlagId() != $flagId) {
                    // Case 1: another flag set to this column
                    $orderFlag->setFlagId($flagId);
                    $this->save($orderFlag);
                }
                // else case 2: this flag is already set to this column
                unset($orderKeys[$orderId]);
            }
        }

        // Case 3: no flag assigned for column
        foreach ($orderKeys as $orderId => $i) {
            /** @var \Amasty\Flags\Model\Order\Flag $orderFlag */
            $orderFlag = $this->flagFactory->create();

            $orderFlag->setData([
                'order_id' => $orderId,
                'flag_id' => $flagId,
                'column_id' => $columnId
            ]);

            $this->save($orderFlag);
        }
    }

    public function massUnassign(array $orderIds, $columnId = null)
    {
        $orderKeys = array_flip($orderIds);

        /** @var \Amasty\Flags\Model\ResourceModel\Order\Flag\Collection $orderFlags */
        $orderFlags = $this->orderFlagCollectionFactory->create();

        if ($columnId) {
            $orderFlags->addFieldToFilter('column_id', $columnId);
        }

        /** @var \Amasty\Flags\Model\Order\Flag $orderFlag */
        foreach ($orderFlags as $orderFlag) {
            if (isset($orderKeys[$orderFlag->getOrderId()])) {
                $this->delete($orderFlag);
            }
        }
    }
}
