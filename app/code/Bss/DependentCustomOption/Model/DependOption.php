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
 * @copyright  Copyright (c) 2017-2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\DependentCustomOption\Model;

use Magento\Framework\Model\AbstractModel;

class DependOption extends AbstractModel
{
    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init(ResourceModel\DependOption::class);
    }

    /**
     * @param int $productId
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getLastIncrementId($productId)
    {
        return $this->_getResource()->getLastIncrementId($productId);
    }

    /**
     * @param int $optionId
     * @return DependOption
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByOptionId($optionId)
    {
        $data = $this->_getResource()->loadByOptionId($optionId);
        return $data? $this->setData($data) : $data;
    }

    /**
     * @param int $optionTypeId
     * @return DependOption
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByOptionTyeId($optionTypeId)
    {
        $data = $this->_getResource()->loadByOptionTyeId($optionTypeId);
        return $data? $this->setData($data) : $data;
    }
}
