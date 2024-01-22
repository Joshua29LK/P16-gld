<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Archive for Magento 2
 */

namespace Amasty\Orderarchive\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Eav\Model\ResourceModel\Helper as ResourceHelper;

class TableStructureSynchronizer
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var ResourceHelper
     */
    private $resourceHelper;

    public function __construct(
        ResourceConnection $resource,
        ResourceHelper $resourceHelper
    ) {
        $this->resource = $resource;
        $this->resourceHelper = $resourceHelper;
    }

    public function execute(string $sourceTable, string $targetTable, $isRemoveFromArchive = false): void
    {
        $connection = $this->resource->getConnection();

        $tableFromFields = $connection->describeTable($sourceTable);
        $tableToFields = $connection->describeTable($targetTable);

        if ($isRemoveFromArchive) {
            $diffFields = array_diff_key($tableToFields, $tableFromFields);
        } else {
            $diffFields = array_diff_key($tableFromFields, $tableToFields);
        }

        if (!empty($diffFields)) {
            foreach ($diffFields as $name => $definition) {
                if ($isRemoveFromArchive) {
                    $connection->dropColumn(
                        $targetTable,
                        $name
                    );
                } else {
                    $connection->addColumn(
                        $targetTable,
                        $name,
                        $this->prepareDefinition($definition)
                    );
                }
            }
        }
    }

    private function prepareDefinition(array $definition): array
    {
        $definition['COMMENT'] = $definition['COMMENT']
            ?? ucwords(str_replace('_', ' ', $definition['COLUMN_NAME']));

        $definition['TYPE'] = $definition['TYPE']
            ?? $this->resourceHelper->getDdlTypeByColumnType($definition['DATA_TYPE']);

        return $definition;
    }
}
