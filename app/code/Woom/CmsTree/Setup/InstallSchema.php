<?php

namespace Woom\CmsTree\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use phpDocumentor\Reflection\Types\This;
use Woom\CmsTree\Api\Data\TreeInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->createTreeTable($setup);
        $this->addRootPageIdColumnToStore($setup);

        $setup->endSetup();
    }

    /**
     * Create tree table that will store CMS Tree entities
     *
     * @param SchemaSetupInterface $setup
     *
     * @return $this
     * @throws \Zend_Db_Exception
     */
    protected function createTreeTable(SchemaSetupInterface $setup)
    {
        if ($setup->tableExists('sambolek_cms_page_tree')) {
            $setup->getConnection()->renameTable(
                'sambolek_cms_page_tree',
                TreeInterface::TREE_CMS_PAGE_TABLE
            );
            return $this;
        }

        /**
        * Add columns to table 'woom_cms_page_tree'
        */
        $table = $setup->getConnection()->newTable(
            $setup->getTable(TreeInterface::TREE_CMS_PAGE_TABLE)
        )->addColumn(
            TreeInterface::TREE_ID,
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true
            ],
            'Tree Id'
        )->addColumn(
            TreeInterface::PARENT_TREE_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Parent Tree Id'
        )->addColumn(
            TreeInterface::PAGE_ID,
            Table::TYPE_SMALLINT,
            null,
            [],
            'Page Id'
        )->addColumn(
            TreeInterface::IDENTIFIER,
            Table::TYPE_TEXT,
            100,
            [],
            'Identifier'
        )->addColumn(
            TreeInterface::TITLE,
            Table::TYPE_TEXT,
            255,
            [],
            'Title'
        )->addColumn(
            TreeInterface::REQUEST_URL,
            Table::TYPE_TEXT,
            255,
            [],
            'Request Url'
        )->addColumn(
            TreeInterface::PATH,
            Table::TYPE_TEXT,
            255,
            [],
            'Tree Path'
        )->addColumn(
            TreeInterface::POSITION,
            Table::TYPE_INTEGER,
            null,
            [
                'nullable' => false,
                'default'  => '0',
            ],
            'Position'
        )->addColumn(
            TreeInterface::LEVEL,
            Table::TYPE_INTEGER,
            null,
            [
                'nullable' => false,
                'default'  => '0',
            ],
            'Tree Level'
        )->addColumn(
            TreeInterface::CHILDREN_COUNT,
            Table::TYPE_INTEGER,
            null,
            [
                'nullable' => false,
                'default'  => 0
            ],
            'Tree Children'
        )->addColumn(
            TreeInterface::IS_IN_MENU,
            Table::TYPE_SMALLINT,
            1,
            [
                'nullable' => false,
                'default'  => 1
            ],
            'Included In Menu Flag'
        )->addColumn(
            TreeInterface::MENU_LABEL,
            Table::TYPE_TEXT,
            255,
            [],
            'Menu Label'
        )->addColumn(
            TreeInterface::MENU_ADD_TYPE,
            Table::TYPE_SMALLINT,
            1,
            [
                'nullable' => false,
                'default'  => 0
            ],
            'Menu Add Type'
        )->addColumn(
            TreeInterface::MENU_ADD_CATEGORY_ID,
            Table::TYPE_INTEGER,
            null,
            [],
            'Menu Add Category Id'
        );

        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Add root_page_id column to store table to identify Root CMS Tree per each store
     *
     * @param SchemaSetupInterface $setup
     *
     * @return $this
     */
    protected function addRootPageIdColumnToStore(SchemaSetupInterface $setup)
    {
        /**
         * Add columns to table 'store'
         */
        $setup->getConnection()->addColumn(
            $setup->getTable(TreeInterface::STORE_TABLE),
            TreeInterface::ROOT_CMS_TREE_ID_COLUMN,
            [
                'type'     => Table::TYPE_INTEGER,
                'length'   => null,
                'unsigned' => true,
                'nullable' => false,
                'default'  => '0',
                'comment'  => 'Root CMS tree ID'
            ]
        );

        return $this;
    }
}
