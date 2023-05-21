<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/
namespace Amasty\Flags\Api;

use Amasty\Flags\Api\Data\ColumnInterface;

interface ColumnRepositoryInterface
{
    /**
     * @param int $id Flag ID.
     * @return ColumnInterface
     */
    public function get($id);

    public function delete(ColumnInterface $entity);

    public function save(ColumnInterface $entity);
}
