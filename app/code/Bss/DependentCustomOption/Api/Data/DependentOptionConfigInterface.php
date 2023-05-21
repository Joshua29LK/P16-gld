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
namespace Bss\DependentCustomOption\Api\Data;

interface DependentOptionConfigInterface
{
    /**
     * Const
     */
    const ENABLE = 'enable';
    const CHILDREN_DISPLAY = 'children_display';
    const MULTIPLE_PARENT = 'multiple_parent';

    /**
     * @return string|bool|int
     */
    public function getEnable();

    /**
     * @return string
     */
    public function getChildrenDisplay();

    /**
     * @return string
     */
    public function getMultipleParent();

    /**
     * @param string|bool|int $enableStatus
     * @return $this
     */
    public function setEnable(string $enableStatus);

    /**
     * @param string $displayChildValues
     * @return $this
     */
    public function setChildrenDisplay(string $displayChildValues);

    /**
     * @param string $displayChildMultiParent
     * @return $this
     */
    public function setMultipleParent(string $displayChildMultiParent);
}
