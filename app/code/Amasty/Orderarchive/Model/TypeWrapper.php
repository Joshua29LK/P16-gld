<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Archive for Magento 2
 */

namespace Amasty\Orderarchive\Model;

/**
 * @since 1.1.0
 */
class TypeWrapper
{
    /**
     * @var ArchiveAbstract
     */
    private $processor;

    /**
     * @var string
     */
    private $sourceTable;

    /**
     * TypeWrapper constructor.
     *
     * @param ArchiveAbstract $processor
     * @param string          $sourceTable
     */
    public function __construct(
        ArchiveAbstract $processor,
        $sourceTable
    ) {
        $this->processor = $processor;
        $this->sourceTable = $sourceTable;
    }

    /**
     * @return string
     */
    public function getSourceTable()
    {
        return $this->sourceTable;
    }

    /**
     * @return ArchiveAbstract
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    public function toArray()
    {
        return ['source_table' => $this->sourceTable, 'target_table' => $this->processor];
    }
}
