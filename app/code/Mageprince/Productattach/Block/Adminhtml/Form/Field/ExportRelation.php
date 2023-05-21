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

namespace Mageprince\Productattach\Block\Adminhtml\Form\Field;

use Magento\Backend\Block\Widget\Button;

class ExportRelation extends Export
{
    /**
     * @return string
     */
    public function getElementHtml()
    {
        /** @var Button $buttonBlock */
        $buttonBlock = $this->getForm()->getParent()->getLayout()->createBlock(Button::class);

        $params = ['website' => $buttonBlock->getRequest()->getParam('website')];

        $url = $this->backendUrl->getUrl('productattach/index/exportrelation', $params);
        $data = [
            'label' => __('Export Products Relations CSV'),
            'onclick' => "setLocation('".$url."' )",
            'class' => '',
        ];

        $html = $buttonBlock->setData($data);

        return $html->toHtml();
    }
}
