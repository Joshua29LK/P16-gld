<?php
namespace Bss\CustomToolTipCO\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{

    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('bss_customizetooltip_custom_option')) {
            $table = $installer->getConnection()
                ->newTable(
                    $installer->getTable('bss_customizetooltip_custom_option')
                )
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'ID'
                )
                ->addColumn(
                    'option_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Option ID'
                )
                ->addColumn(
                    'content',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '2M',
                    [],
                    'Tooltip Content'
                )
                ->addForeignKey(
                      $installer->getFkName('bss_customizetooltip_custom_option', 'option_type_id', 'catalog_product_option', 'option_id'),
                      'option_id',
                      $installer->getTable('catalog_product_option'),
                      'option_id',
                      \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )->addIndex(
                    $installer->getIdxName('catalog_product_option', ['id']),
                    ['id']
                )
                ->setComment(
                    'Bss customize tool tip custom option'
                );
                $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}
