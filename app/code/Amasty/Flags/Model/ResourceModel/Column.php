<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Flags\Model\ResourceModel;

use Amasty\Flags\Model\Column as ColumnModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Column extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('amasty_flags_column', 'id');
    }

    /**
     * @param ColumnModel $object
     * @param array       $ids
     * @param bool        $deletePrevious
     */
    public function assignFlags(ColumnModel $object, array $ids, $deletePrevious = false)
    {
        $table = $this->getTable('amasty_flags_flag_column');

        if ($deletePrevious) {
            $this->getConnection()->delete($table, 'column_id = ' . ((int)$object->getId()));
        }

        foreach ($ids as $id) {
            $this->getConnection()->insertOnDuplicate(
                $table,
                ['column_id' => $object->getId(), 'flag_id' => $id]
            );
        }
    }

    /**
     * @param ColumnModel $object
     *
     * @return array
     */
    public function getAppliedFlagIds(ColumnModel $object): array
    {
        $select = $this->getConnection()->select()
            ->from(
                $this->getTable('amasty_flags_flag_column'),
                'flag_id'
            )->joinLeft(
                ['flags' => $this->getTable('amasty_flags_flag')],
                'flag_id = flags.id',
                ['priority']
            )->where('column_id = ?', $object->getId())
            ->order('priority');

        return $this->getConnection()->fetchCol($select);
    }
}
