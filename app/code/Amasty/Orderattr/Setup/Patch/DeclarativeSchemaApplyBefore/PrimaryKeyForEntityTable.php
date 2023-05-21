<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Custom Checkout Fields for Magento 2
 */

namespace Amasty\Orderattr\Setup\Patch\DeclarativeSchemaApplyBefore;

use Amasty\Orderattr\Model\Entity\EntityData;
use Amasty\Orderattr\Model\ResourceModel\Entity\Entity;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * compatibility with m2.4.2
 * Declarative Schema can't add autoincrement column if existing table hasn't primary key
 */
class PrimaryKeyForEntityTable implements SchemaPatchInterface
{
    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    public function __construct(
        SchemaSetupInterface $schemaSetup
    ) {
        $this->schemaSetup = $schemaSetup;
    }

    public function apply(): void
    {
        $connection = $this->schemaSetup->getConnection();

        $tableName = $this->schemaSetup->getTable(Entity::TABLE_NAME);

        if ($connection->isTableExists($tableName) && !$connection->tableColumnExists($tableName, EntityData::ROW_ID)) {
            $connection->addColumn($tableName, EntityData::ROW_ID, [
                'type' => Table::TYPE_INTEGER,
                'identity' => true,
                'primary' => true,
                'unsigned' => true,
                'nullable' => false,
                'comment' => 'Row ID'
            ]);
        }
    }

    public function getAliases(): array
    {
        return [];
    }

    public static function getDependencies(): array
    {
        return [];
    }
}
