<?php

/**
 * MagePrince
 * Copyright (C) 2020 Mageprince <info@mageprince.com>
 *
 * @package Mageprince_Productattach
 * @copyright Copyright (c) 2020 Mageprince (http://www.mageprince.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MagePrince <info@mageprince.com>
 */

namespace Mageprince\Productattach\Model\Import;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\ImportExport\Helper\Data as ImportHelper;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Magento\ImportExport\Model\ResourceModel\Import\Data;

class AttachmentsRelation extends AbstractEntity
{
    const ENTITY_CODE = 'attachments_relation';
    const TABLE = 'prince_productattach_relation';
    const ENTITY_ID_COLUMN = 'productattach_id';
    const PRODUCT_COLUMN = 'products';

    /**
     * If we should check column names
     */
    protected $needColumnCheck = true;

    /**
     * Need to log in import history
     */
    protected $logInHistory = true;

    /**
     * Permanent entity columns.
     */
    protected $_permanentAttributes = [
        self::ENTITY_ID_COLUMN
    ];

    /**
     * Valid column names
     */
    protected $validColumnNames = [
        self::ENTITY_ID_COLUMN,
        self::PRODUCT_COLUMN
    ];

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * AttachmentsRelation constructor.
     *
     * @param JsonHelper $jsonHelper
     * @param ImportHelper $importExportData
     * @param Data $importData
     * @param ResourceConnection $resource
     * @param Helper $resourceHelper
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     */
    public function __construct(
        JsonHelper $jsonHelper,
        ImportHelper $importExportData,
        Data $importData,
        ResourceConnection $resource,
        Helper $resourceHelper,
        ProcessingErrorAggregatorInterface $errorAggregator
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->resource = $resource;
        $this->connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
    }

    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return static::ENTITY_CODE;
    }

    /**
     * Get available columns
     *
     * @return array
     */
    public function getValidColumnNames()
    {
        return $this->validColumnNames;
    }

    /**
     * Row validation
     *
     * @param array $rowData
     * @param int $rowNum
     * @return bool
     * @throws \Exception
     */
    public function validateRow(array $rowData, $rowNum)
    {
        if (!$rowData[self::ENTITY_ID_COLUMN]) {
            throw new \Exception(__('Attachment id must not be empty'));
        }

        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }

        $this->_validatedRows[$rowNum] = true;

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * Import data
     *
     * @return bool
     * @throws \Exception
     */
    protected function _importData()
    {
        switch ($this->getBehavior()) {
            case Import::BEHAVIOR_DELETE:
                throw new \Exception(
                    'Import Behaviour: Delete Not Allowed.
                    Please delete assigned attachment products from the attachment edit page.'
                );
                break;
            case Import::BEHAVIOR_REPLACE:
                throw new \Exception('Import Behaviour: Replace Not Allowed. Please use only Add/Update');
                break;
            case Import::BEHAVIOR_APPEND:
                $this->saveAndReplaceEntity();
                break;
        }

        return true;
    }

    /**
     * Save and replace entities
     *
     * @return void
     * @throws \Exception
     */
    private function saveAndReplaceEntity()
    {
        $behavior = $this->getBehavior();
        $rows = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityList = [];

            foreach ($bunch as $rowNum => $row) {

                if (!$row[self::ENTITY_ID_COLUMN]) {
                    throw new \Exception('We can\'t find required columns data: productattach_id');
                }

                if (!$this->validateRow($row, $rowNum)) {
                    continue;
                }

                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }

                $rowId = $row[static::ENTITY_ID_COLUMN];
                $rows[] = $rowId;
                $columnValues = [];

                foreach ($this->getAvailableColumns() as $columnKey) {
                    $columnValues[$columnKey] = $row[$columnKey];
                }

                $entityList[$rowId][] = $columnValues;
                $this->countItemsCreated += (int) !isset($row[static::ENTITY_ID_COLUMN]);
                $this->countItemsUpdated += (int) isset($row[static::ENTITY_ID_COLUMN]);
            }

            if (Import::BEHAVIOR_APPEND === $behavior) {
                $this->saveEntityFinish($entityList);
            }
        }
    }

    /**
     * Save product relation
     *
     * @param $attachmentId
     * @param $productIds
     */
    public function saveProductsRelation($attachmentId, $productIds = null)
    {
        try {
            $oldProducts = (array) $this->getProducts($attachmentId);
            $newProducts = (array) $productIds;

            $connection = $this->connection;

            $table = $connection->getTableName(self::TABLE);
            $insert = array_diff($newProducts, $oldProducts);
            $delete = array_diff($oldProducts, $newProducts);

            if ($delete) {
                $where = [
                    'productattach_id = ?' => (int)$attachmentId,
                    'product_id IN (?)' => $delete
                ];
                $connection->delete($table, $where);
            }

            if ($insert) {
                $data = [];
                foreach ($insert as $productId) {
                    $data[] = [
                        'productattach_id' => (int)$attachmentId,
                        'product_id' => (int)$productId
                    ];
                }
                $connection->insertMultiple($table, $data);
            }
        } catch (\Exception $e) {
            throw new $e('Attachment or product not found. Please check again import sheet.');
        }
    }

    /**
     * Get products by attachment id
     *
     * @param $attachmentId
     * @return array
     */
    public function getProducts($attachmentId)
    {
        $tbl = $this->resource->getTableName(self::TABLE);
        $select = $this->connection->select()->from(
            $tbl,
            ['product_id']
        )
            ->where(
                'productattach_id = ?',
                (int)$attachmentId
            );

        return $this->connection->fetchCol($select);
    }

    /**
     * Save entities
     *
     * @param array $entityData
     * @return bool
     */
    private function saveEntityFinish(array $entityData)
    {
        if ($entityData) {
            $rows = [];

            foreach ($entityData as $entityRows) {
                foreach ($entityRows as $row) {
                    $rows[] = $row;
                }
            }

            if ($rows) {
                foreach ($rows as $row) {
                    if ($row[self::PRODUCT_COLUMN] != '') {
                        $products = explode('|', $row[self::PRODUCT_COLUMN]);
                        $this->saveProductsRelation($row[self::ENTITY_ID_COLUMN], $products);
                    } else {
                        $this->saveProductsRelation($row[self::ENTITY_ID_COLUMN]);
                    }
                }
                return true;
            }

            return false;
        }
    }

    /**
     * Delete entities
     *
     * @param array $entityIds
     * @return bool
     */
    private function deleteEntityFinish(array $entityIds)
    {
        if ($entityIds) {
            try {
                $this->countItemsDeleted += $this->connection->delete(
                    $this->connection->getTableName(static::TABLE),
                    $this->connection->quoteInto(static::ENTITY_ID_COLUMN . ' IN (?)', $entityIds)
                );
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Get available columns
     * @return array
     */
    private function getAvailableColumns()
    {
        return $this->validColumnNames;
    }
}
