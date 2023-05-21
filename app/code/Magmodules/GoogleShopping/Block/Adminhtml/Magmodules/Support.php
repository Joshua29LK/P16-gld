<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Block\Adminhtml\Magmodules;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Support extends Field
{

    public const MODULE_CODE = 'googleshopping-magento2';
    public const SUPPORT_LINK = 'https://www.magmodules.eu/help/' . self::MODULE_CODE;
    public const MANUAL_LINK = '/configure-google-shopping-magento2';
    
    /**
     * @inheritDoc
     */
    public function _getElementHtml(AbstractElement $element)
    {
        $html = sprintf(
            '<a href="%s" class="support-link">%s</a> &nbsp; | &nbsp; <a href="%s" class="support-link">%s</a>',
            self::SUPPORT_LINK. self::MANUAL_LINK,
            __('Online Manual'),
            self::SUPPORT_LINK,
            __('FAQ')
        );

        $element->setData('text', $html);
        return parent::_getElementHtml($element);
    }

    /**
     * @inheritDoc
     */
    public function _renderScopeLabel(AbstractElement $element)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function _renderInheritCheckbox(AbstractElement $element)
    {
        return '';
    }
}
