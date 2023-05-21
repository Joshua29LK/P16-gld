<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/
namespace Amasty\Flags\Api\Data;

interface FlagInterface
{
    public const ID = 'id';
    public const NAME = 'name';
    public const IMAGE_NAME = 'image_name';
    public const PRIORITY = 'priority';
    public const NOTE = 'note';
    public const APPLY_COLUMN = 'apply_column';
    public const APPLY_STATUS = 'apply_status';
    public const APPLY_SHIPPING = 'apply_shipping';
    public const APPLY_PAYMENT = 'apply_payment';

    /**
     * @return int
     */
    public function getId();
    
    /**
     * @param int $id
     * @return \Amasty\Flags\Api\Data\FlagInterface
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getName();
    
    /**
     * @param string $name
     * @return \Amasty\Flags\Api\Data\FlagInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getImageName();
    
    /**
     * @param string $imageName
     * @return \Amasty\Flags\Api\Data\FlagInterface
     */
    public function setImageName($imageName);

    /**
     * @return int
     */
    public function getPriority();
    
    /**
     * @param int $priority
     * @return \Amasty\Flags\Api\Data\FlagInterface
     */
    public function setPriority($priority);

    /**
     * @return string
     */
    public function getNote();
    
    /**
     * @param string $note
     * @return \Amasty\Flags\Api\Data\FlagInterface
     */
    public function setNote($note);

    /**
     * @return int|null
     */
    public function getApplyColumn();
    
    /**
     * @param int|null $applyColumn
     * @return \Amasty\Flags\Api\Data\FlagInterface
     */
    public function setApplyColumn($applyColumn);

    /**
     * @return string
     */
    public function getApplyStatus();
    
    /**
     * @param string $applyStatus
     * @return \Amasty\Flags\Api\Data\FlagInterface
     */
    public function setApplyStatus($applyStatus);

    /**
     * @return string
     */
    public function getApplyShipping();
    
    /**
     * @param string $applyShipping
     * @return \Amasty\Flags\Api\Data\FlagInterface
     */
    public function setApplyShipping($applyShipping);

    /**
     * @return string
     */
    public function getApplyPayment();
    
    /**
     * @param string $applyPayment
     * @return \Amasty\Flags\Api\Data\FlagInterface
     */
    public function setApplyPayment($applyPayment);
}
