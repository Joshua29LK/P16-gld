<?php

/**
 * MagePrince
 * Copyright (C) 2020 Mageprince <info@mageprince.com>
 *
 * @package Mageprince_Productattach
 * @copyright Copyright (c) 2020 Mageprince (http://www.mageprince.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MagePrince <info@mageprince.com>
 */

namespace Mageprince\Productattach\Block\Adminhtml\System\Config\Field;

class ImportUserGuid extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Template path
     *
     * @var string
     */
    protected $_template = 'Mageprince_Productattach::system/config/Field/ImportUserGuid.phtml';

    /**
     * Render fieldset html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_decorateRowHtml($element, "<td colspan='3'>" . $this->toHtml() . '</td>');
    }
}
