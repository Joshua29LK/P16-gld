<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/
namespace Amasty\Flags\Api\Data;

interface ColumnInterface
{
    public const ID = 'id';
    public const NAME = 'name';
    public const POSITION = 'position';
    public const COMMENT = 'comment';

    /**
     * @return int
     */
    public function getId();
    
    /**
     * @param int $id
     * @return \Amasty\Flags\Api\Data\ColumnInterface
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getName();
    
    /**
     * @param string $name
     * @return \Amasty\Flags\Api\Data\ColumnInterface
     */
    public function setName($name);

    /**
     * @return int
     */
    public function getPosition();
    
    /**
     * @param int $position
     * @return \Amasty\Flags\Api\Data\ColumnInterface
     */
    public function setPosition($position);

    /**
     * @return string
     */
    public function getComment();
    
    /**
     * @param string $comment
     * @return \Amasty\Flags\Api\Data\ColumnInterface
     */
    public function setComment($comment);
}
