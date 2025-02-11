<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Checkout Fields for Magento 2
 */

namespace Amasty\Orderattr\Model\ResourceModel\Entity\EntityData;

/**
 * @method \Amasty\Orderattr\Model\ResourceModel\Entity\Entity getResource()
 */
class Collection extends \Magento\Eav\Model\Entity\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init(
            \Amasty\Orderattr\Model\Entity\EntityData::class,
            \Amasty\Orderattr\Model\ResourceModel\Entity\Entity::class
        );
    }
}
