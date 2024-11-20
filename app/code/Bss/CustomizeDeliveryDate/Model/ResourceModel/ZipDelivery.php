<?php

namespace Bss\CustomizeDeliveryDate\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ZipDelivery extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('bss_zip_delivery', 'zip_id');
    }

    /**
     * Delete records by zip IDs
     *
     * @param array $zipIds
     * @return int Number of deleted rows
     * @throws \Exception
     */
    public function deleteZipByIds(array $zipIds): int
    {
        $connection = $this->getConnection();

        try {
            $deletedCount = $connection->delete(
                $this->getMainTable(),
                ['zip_id IN (?)' => $zipIds]
            );
        } catch (\Exception $e) {
            throw new \Exception(__('Error deleting zip delivery items: %1', $e->getMessage()));
        }

        return $deletedCount;
    }

    /**
     * Truncate the bss_zip_delivery table.
     *
     * @return void
     */
    public function truncateData()
    {
        $connection = $this->getConnection();
        try {
            $connection->truncateTable($this->getMainTable());
        } catch (\Exception $e) {
            $this->logger->error(__('Error during truncating the table: %1', $e->getMessage()));
        }
    }
}
