<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/
namespace Amasty\Flags\Api;

use Amasty\Flags\Api\Data\FlagInterface;

interface FlagRepositoryInterface
{
    /**
     * @param int $id Flag ID.
     * @return FlagInterface
     */
    public function get($id);

    public function delete(FlagInterface $entity);

    public function save(FlagInterface $entity);
}
