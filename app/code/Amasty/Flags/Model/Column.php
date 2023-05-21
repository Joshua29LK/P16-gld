<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/
namespace Amasty\Flags\Model;

use Amasty\Flags\Api\Data\ColumnInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Column model
 *
 * @method \Amasty\Flags\Model\ResourceModel\Column getResource()
 */

class Column extends AbstractModel implements ColumnInterface
{
    protected function _construct()
    {
        $this->_init(\Amasty\Flags\Model\ResourceModel\Column::class);
    }

    public function assignFlags(array $ids)
    {
        $this->getResource()->assignFlags($this, $ids);
        return $this;
    }

    public function reassignFlags(array $ids)
    {
        $this->getResource()->assignFlags($this, $ids, true);
        return $this;
    }

    public function getAppliedFlagIds()
    {
        return $this->getResource()->getAppliedFlagIds($this);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData(ColumnInterface::NAME);
    }

    /**
     * @param string $name
     * @return ColumnInterface
     */
    public function setName($name)
    {
        return $this->setData(ColumnInterface::NAME, $name);
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->getData(ColumnInterface::POSITION);
    }

    /**
     * @param int $position
     * @return ColumnInterface
     */
    public function setPosition($position)
    {
        return $this->setData(ColumnInterface::POSITION, $position);
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->getData(ColumnInterface::COMMENT);
    }

    /**
     * @param string $comment
     * @return ColumnInterface
     */
    public function setComment($comment)
    {
        return $this->setData(ColumnInterface::COMMENT, $comment);
    }
}
