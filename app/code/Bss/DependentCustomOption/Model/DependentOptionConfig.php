<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_DependentCustomOption
 * @author     Extension Team
 * @copyright  Copyright (c) 2020-2021 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\DependentCustomOption\Model;

use Bss\DependentCustomOption\Api\Data\DependentOptionConfigInterface;
use Magento\Framework\DataObject;

class DependentOptionConfig extends DataObject implements DependentOptionConfigInterface
{
    /**
     * @inheritDoc
     */
    public function getEnable()
    {
        return $this->getData(self::ENABLE);
    }

    /**
     * @inheritDoc
     */
    public function getChildrenDisplay()
    {
        return $this->getData(self::CHILDREN_DISPLAY);
    }

    /**
     * @inheritDoc
     */
    public function getMultipleParent()
    {
        return $this->getData(self::MULTIPLE_PARENT);
    }

    /**
     * @inheritDoc
     */
    public function setEnable(string $enableStatus)
    {
        return $this->setData(self::ENABLE, $enableStatus);
    }

    /**
     * @inheritDoc
     */
    public function setChildrenDisplay(string $displayChildValues)
    {
        return $this->setData(self::CHILDREN_DISPLAY, $displayChildValues);
    }

    /**
     * @inheritDoc
     */
    public function setMultipleParent(string $displayChildMultiParent)
    {
        return $this->setData(self::MULTIPLE_PARENT, $displayChildMultiParent);
    }
}
