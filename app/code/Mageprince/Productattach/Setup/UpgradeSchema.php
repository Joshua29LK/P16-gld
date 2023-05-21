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

namespace Mageprince\Productattach\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table as DdlTable;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            $this->updateAttachmentColumns($setup);
            $this->createProductRelationTable($setup);
            $this->convertOldProductRelationData($setup);
        }
    }

    /**
     * Add new columns
     *
     * @param SchemaSetupInterface $setup
     */
    public function updateAttachmentColumns($setup)
    {
        $setup->startSetup();
        $mainAttachmentTable = $setup->getTable('prince_productattach');
        $connection = $setup->getConnection();

        if ($connection->tableColumnExists($mainAttachmentTable, 'published_at')) {
            $connection->dropColumn($mainAttachmentTable, 'published_at');
        }

        $connection->addColumn(
            $mainAttachmentTable,
            'created_at',
            [
                'type' => DdlTable::TYPE_TIMESTAMP,
                'nullable' => false,
                'default' => DdlTable::TIMESTAMP_INIT,
                'comment' => 'Created At'
            ]
        );

        $connection->addColumn(
            $mainAttachmentTable,
            'updated_at',
            [
                'type' => DdlTable::TYPE_TIMESTAMP,
                'nullable' => false,
                'default' => DdlTable::TIMESTAMP_INIT_UPDATE,
                'comment' => 'Created At'
            ]
        );
        $setup->endSetup();
    }

    /**
     * Create attachment product relation table
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    public function createProductRelationTable($setup)
    {
        $setup->startSetup();
        $table = $setup->getConnection()
            ->newTable($setup->getTable('prince_productattach_relation'))
            ->addColumn(
                'productattach_id',
                DdlTable::TYPE_INTEGER,
                10,
                ['nullable' => false, 'unsigned' => true]
            )
            ->addColumn(
                'product_id',
                DdlTable::TYPE_INTEGER,
                10,
                ['nullable' => false, 'unsigned' => true],
                'Magento Product Id'
            )
            ->addForeignKey(
                $setup->getFkName(
                    'prince_productattach',
                    'productattach_id',
                    'prince_productattach_relation',
                    'productattach_id'
                ),
                'productattach_id',
                $setup->getTable('prince_productattach'),
                'productattach_id',
                DdlTable::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName(
                    'prince_productattach_relation',
                    'product_id',
                    'catalog_product_entity',
                    'entity_id'
                ),
                'product_id',
                $setup->getTable('catalog_product_entity'),
                'entity_id',
                DdlTable::ACTION_CASCADE
            )
            ->setComment('Mageprince Product Attachment relation table');

        $setup->getConnection()->createTable($table);
        $setup->endSetup();
    }

    /**
     * Convert old product relation data
     *
     * @param SchemaSetupInterface $setup
     */
    public function convertOldProductRelationData($setup)
    {
        $setup->startSetup();
        try {
            $connection = $setup->getConnection();
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $mainAttachmentTable = $setup->getTable('prince_productattach');
            $select = $connection->select()->from($mainAttachmentTable);
            $attachments = $connection->fetchAll($select);
            $data = [];
            foreach ($attachments as $attachment) {
                if ($attachment['products']) {
                    $products = explode('&', $attachment['products']);
                    $attachmentId = $attachment['productattach_id'];
                    foreach ($products as $productId) {
                        $data[] = [
                            'productattach_id' => (int)$attachmentId,
                            'product_id' => (int)$productId
                        ];
                    }
                }
            }
            if ($data) {
                $connection->insertMultiple('prince_productattach_relation', $data);
            }
        } catch (\Exception $e) {
            null; // Skip mismatch data
        }
        $connection->query('SET FOREIGN_KEY_CHECKS=1');
        $setup->endSetup();
    }
}
