<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

namespace Amasty\Flags\Model;

use Amasty\Flags\Api\ColumnRepositoryInterface;
use Amasty\Flags\Api\Data\ColumnInterface;
use Amasty\Flags\Model\ResourceModel\Column as ColumnResource;
use Amasty\Flags\Model\ColumnFactory;

class ColumnRepository implements ColumnRepositoryInterface
{
    /**
     * @var ResourceModel\Column
     */
    private $columnResource;
    /**
     * @var ColumnFactory
     */
    private $columnFactory;

    public function __construct(
        ColumnResource $columnResource,
        ColumnFactory $columnFactory
    ) {
        $this->columnResource = $columnResource;
        $this->columnFactory = $columnFactory;
    }

    /**
     * @param int $id Column ID.
     *
     * @return ColumnInterface
     */
    public function get($id)
    {
        $model = $this->columnFactory->create();

        $this->columnResource->load($model, $id);

        return $model;
    }

    public function delete(ColumnInterface $entity)
    {
        return $this->columnResource->delete($entity);
    }

    public function save(ColumnInterface $entity)
    {
        return $this->columnResource->save($entity);
    }
}
