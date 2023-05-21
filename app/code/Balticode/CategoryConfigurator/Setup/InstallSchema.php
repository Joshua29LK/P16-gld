<?php

namespace Balticode\CategoryConfigurator\Setup;

use Balticode\CategoryConfigurator\Api\Data\ProductGlassShapeInterface;
use Balticode\CategoryConfigurator\Model\ResourceModel\Configurator;
use Balticode\CategoryConfigurator\Model\ResourceModel\ProductGlassShape;
use Balticode\CategoryConfigurator\Model\ResourceModel\Step;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Balticode\CategoryConfigurator\Api\Data\ConfiguratorInterface;
use Balticode\CategoryConfigurator\Api\Data\StepInterface;
use Magento\Framework\DB\Ddl\Table;
use Zend_Db_Exception;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->createConfiguratorTable($setup);
        $this->createStepTable($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    protected function createConfiguratorTable(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable(Configurator::TABLE);

        if ($setup->getConnection()->isTableExists($tableName)) {
            return;
        }

        $table = $setup->getConnection()->newTable($tableName);

        $table->addColumn(
            ConfiguratorInterface::CONFIGURATOR_ID,
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'nullable' => false,
                'primary' => true,
                'unsigned' => true
            ],
            'Entity ID'
        );

        $table->addColumn(
            ConfiguratorInterface::TITLE,
            Table::TYPE_TEXT,
            255,
            [
                'nullable' => false,
                'unsigned' => true
            ],
            'Title'
        );

        $table->addColumn(
            ConfiguratorInterface::DESCRIPTION,
            Table::TYPE_TEXT,
            null,
            [
                'nullable' => true,
                'unsigned' => true
            ],
            'Description'
        );

        $table->addColumn(
            ConfiguratorInterface::ENABLE,
            Table::TYPE_BOOLEAN,
            null,
            [
                'nullable' => false,
                'default' => 0
            ],
            'Enable'
        );

        $table->addColumn(
            ConfiguratorInterface::CATEGORY_ID,
            Table::TYPE_INTEGER,
            null,
            [
                'nullable' => false,
                'unsigned' => true
            ],
            'Category ID'
        );

        $table->addColumn(
            ConfiguratorInterface::IMAGE_NAME,
            Table::TYPE_TEXT,
            null,
            [
                'nullable' => true,
                'unsigned' => true
            ],
            'Image Name'
        );

        $setup->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    protected function createStepTable(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable(Step::TABLE);

        if ($setup->getConnection()->isTableExists($tableName)) {
            return;
        }

        $table = $setup->getConnection()->newTable($tableName);

        $table->addColumn(
            StepInterface::STEP_ID,
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'nullable' => false,
                'primary' => true,
                'unsigned' => true,
            ],
            'Entity ID'
        );

        $table->addColumn(
            StepInterface::CONFIGURATOR_ID,
            Table::TYPE_INTEGER,
            null,
            [
                'nullable' => false,
                'primary' => true,
                'unsigned' => true
            ],
            'Configurator ID'
        );

        $table->addColumn(
            StepInterface::ENABLE,
            Table::TYPE_BOOLEAN,
            null,
            [
                'nullable' => false,
                'default' => 0
            ],
            'Enable'
        );

        $table->addColumn(
            StepInterface::TITLE,
            Table::TYPE_TEXT,
            255,
            [
                'nullable' => false,
                'unsigned' => true
            ],
            'Title'
        );

        $table->addColumn(
            StepInterface::DESCRIPTION,
            Table::TYPE_TEXT,
            null,
            [
                'nullable' => true,
                'unsigned' => true
            ],
            'Description'
        );

        $table->addColumn(
            StepInterface::INFO,
            Table::TYPE_TEXT,
            null,
            [
                'nullable' => true,
                'unsigned' => true
            ],
            'Info'
        );

        $table->addColumn(
            StepInterface::CATEGORY_ID,
            Table::TYPE_INTEGER,
            null,
            [
                'nullable' => true,
                'unsigned' => true
            ],
            'Category Id'
        );

        $table->addColumn(
            StepInterface::PARENT_ID,
            Table::TYPE_INTEGER,
            null,
            [
                'nullable' => true,
                'unsigned' => true
            ],
            'Step Id'
        );

        $table->addColumn(
            StepInterface::SECOND_PARENT_ID,
            Table::TYPE_INTEGER,
            null,
            [
                'nullable' => true,
                'unsigned' => true
            ],
            'Step Id'
        );

        $table->addColumn(
            StepInterface::SORT_ORDER,
            Table::TYPE_INTEGER,
            null,
            [
                'nullable' => true,
                'unsigned' => false
            ],
            'Sort order'
        );

        $table->addColumn(
            StepInterface::TYPE,
            Table::TYPE_SMALLINT,
            1,
            [
                'nullable' => false,
                'unsigned' => true
            ],
            'Type'
        );

        $table->addColumn(
            StepInterface::FULL_WIDTH,
            Table::TYPE_BOOLEAN,
            null,
            [
                'nullable' => false,
                'default' => 0
            ],
            'Is Full Width'
        );

        $table->addForeignKey(
            'FK_BALTICODE_STEP_CONFIGURATOR',
            StepInterface::CONFIGURATOR_ID,
            $setup->getTable(Configurator::TABLE),
            ConfiguratorInterface::CONFIGURATOR_ID,
            Table::ACTION_CASCADE
        );

        $setup->getConnection()->createTable($table);
    }
}
