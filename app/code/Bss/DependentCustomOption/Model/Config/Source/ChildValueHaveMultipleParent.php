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
namespace Bss\DependentCustomOption\Model\Config\Source;

use Magento\Framework\DB\Ddl\Table;

class ChildValueHaveMultipleParent extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options =
            [
                ['label' => __('Use Global Config'), 'value' => 'global'],
                ['label' => __('When at least one parent value is selected'), 'value' => 'atleast_one'],
                ['label' => __('When all parent values are selected'), 'value' => 'all'],
            ];
        return $this->_options;
    }

    /**
     * Get a text for option value
     *
     * @param string $value
     * @return string|bool
     */
    public function getOptionText($value)
    {
        if (!$this->_options) {
            $this->_options = $this->getAllOptions();
        }
        if (array_key_exists($value, $this->_options)) {
            return $this->_options[$value];
        }
        return false;
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColumns()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        return [
            $attributeCode => [
                'unsigned' => false,
                'default' => null,
                'extra' => null,
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Depend child values that have multiple parent values  ' . $attributeCode . ' column',
            ],
        ];
    }
}
