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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $setup->startSetup();

        $productAttachTable = $setup->getConnection()->newTable($setup->getTable('prince_productattach'));

        $productAttachTable->addColumn(
            'productattach_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        );

        $productAttachTable->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Name'
        );

        $productAttachTable->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true,'default' => null],
            'Description'
        );

        $productAttachTable->addColumn(
            'file',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true,'default' => null],
            'Content'
        );

        $productAttachTable->addColumn(
            'file_ext',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true,'default' => null],
            'File Extension'
        );

        $productAttachTable->addColumn(
            'url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true,'default' => null],
            'Productattach image media path'
        );

        $productAttachTable->addColumn(
            'store',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true,'default' => null],
            'Store'
        );

        $productAttachTable->addColumn(
            'customer_group',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true,'default' => null],
            'Customer Group'
        );

        $productAttachTable->addColumn(
            'products',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true,'default' => null],
            'Assigned Products'
        );

        $productAttachTable->addColumn(
            'active',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Active'
        );

        $productAttachTable->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false],
            'Created At'
        );

        $installer->getConnection()->createTable($productAttachTable);

        $fileIconTable = $setup->getConnection()->newTable($setup->getTable('prince_productattach_fileicon'));

        $fileIconTable->addColumn(
            'fileicon_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity ID'
        );

        $fileIconTable->addColumn(
            'icon_ext',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'icon_ext'
        );

        $fileIconTable->addColumn(
            'icon_image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'icon_image'
        );

        $setup->getConnection()->createTable($fileIconTable);
        $setup->endSetup();
    }
}
