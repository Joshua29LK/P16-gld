<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */


namespace Amasty\Flags\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $this->createColumnTable($installer)
            ->createFlagTable($installer)
            ->createFlagColumnTable($installer)
            ->createOrderFlagTable($installer);

        $installer->endSetup();
    }

    /**
     * Create table 'amasty_flags_column'
     *
     * @param SchemaSetupInterface $installer
     *
     * @return $this
     */
    protected function createColumnTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_flags_column'))
            ->addColumn(
                'id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Column Name'
            )
            ->addColumn(
                'position',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Column Position'
            )
            ->addColumn(
                'comment',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Comment'
            )
            ->setComment('Amasty Flags Column Table');
        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'amasty_flags_flag_column'
     *
     * @param SchemaSetupInterface $installer
     *
     * @return $this
     */
    protected function createFlagColumnTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_flags_flag_column'))
            ->addColumn(
                'column_id',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Column ID'
            )
            ->addColumn(
                'flag_id',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Flag ID'
            )
            ->addIndex(
                $installer->getIdxName(
                    'amasty_flags_flag_column',
                    ['column_id', 'flag_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['column_id', 'flag_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_flags_flag_column',
                    'column_id',
                    'amasty_flags_column',
                    'id'
                ),
                'column_id',
                $installer->getTable('amasty_flags_column'),
                'id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_flags_flag_column',
                    'flag_id',
                    'amasty_flags_flag',
                    'id'
                ),
                'flag_id',
                $installer->getTable('amasty_flags_flag'),
                'id',
                Table::ACTION_CASCADE
            )
            ->setComment('Amasty Flags Column-Flag Relation Table');
        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'amasty_flags_order_flag'
     *
     * @param SchemaSetupInterface $installer
     *
     * @return $this
     */
    protected function createOrderFlagTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_flags_order_flag'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Column ID'
            )
            ->addColumn(
                'flag_id',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Flag ID'
            )
            ->addColumn(
                'column_id',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Column ID'
            )
            ->addColumn(
                'note',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Note'
            )
            ->addIndex(
                $installer->getIdxName(
                    'amasty_flags_order_flag',
                    ['column_id', 'order_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['column_id', 'order_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_flags_order_flag',
                    'column_id',
                    'amasty_flags_column',
                    'id'
                ),
                'column_id',
                $installer->getTable('amasty_flags_column'),
                'id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_flags_order_flag',
                    'order_id',
                    'sales_order',
                    'entity_id'
                ),
                'order_id',
                $installer->getTable('sales_order'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_flags_order_flag',
                    'flag_id',
                    'amasty_flags_flag',
                    'id'
                ),
                'flag_id',
                $installer->getTable('amasty_flags_flag'),
                'id',
                Table::ACTION_CASCADE
            )
            ->setComment('Amasty Flags Order-Flag Relation Table');
        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'amasty_flags_flag'
     *
     * @param SchemaSetupInterface $installer
     *
     * @return $this
     */
    protected function createFlagTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_flags_flag'))
            ->addColumn(
                'id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Flag Name'
            )
            ->addColumn(
                'image_name',
                Table::TYPE_TEXT,
                127,
                ['nullable' => false],
                'Image File Name'
            )
            ->addColumn(
                'priority',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Priority'
            )
            ->addColumn(
                'note',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Note'
            )
            ->addColumn(
                'apply_column',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => true, 'default' => null, 'unsigned' => true],
                'Automatically Applied To Column'
            )
            ->addColumn(
                'apply_status',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Automatically Applied For Statuses'
            )
            ->addColumn(
                'apply_shipping',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Automatically Applied For Shipping Methods'
            )
            ->addColumn(
                'apply_payment',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Automatically Applied For Payment Methods'
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_flags_flag',
                    'apply_column',
                    'amasty_flags_column',
                    'id'
                ),
                'apply_column',
                $installer->getTable('amasty_flags_column'),
                'id',
                Table::ACTION_SET_NULL
            )
            ->setComment('Amasty Flags Flag Table');
        $installer->getConnection()->createTable($table);

        return $this;
    }
}
