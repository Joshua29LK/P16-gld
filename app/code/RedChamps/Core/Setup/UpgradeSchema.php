<?php
namespace RedChamps\Core\Setup;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $connection = $installer->getConnection();

        if (version_compare($context->getVersion(), '1.0.4') < 0) {
            /**
             * Create table 'redchamps_timestamp'
             */
            $timeStampTable = $connection->newTable(
                $installer->getTable('redchamps_timestamp')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'type',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Type'
            )->addColumn(
                'last_processed',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Last Processed'
            );
            $connection->createTable($timeStampTable);
        }
    }
}
