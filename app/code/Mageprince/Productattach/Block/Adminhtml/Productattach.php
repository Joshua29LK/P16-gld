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

namespace Mageprince\Productattach\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Productattach extends Container
{
    /**
     * Constructor
     *
     * @return void
     */
    public function _construct()
    {
        $this->_controller = 'adminhtml_productattach';
        $this->_blockGroup = 'Mageprince_Productattach';
        $this->_headerText = __('Product Attachments');
        $this->_addButtonLabel = __('Add New Attachment');
        parent::_construct();
        if ($this->_isAllowedAction('Mageprince_Productattach::manage')) {
            $this->buttonList->update('add', 'label', __('Add New Attachment'));
        } else {
            $this->buttonList->remove('add');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    public function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
