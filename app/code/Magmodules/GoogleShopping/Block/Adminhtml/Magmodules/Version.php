<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Block\Adminhtml\Magmodules;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magmodules\GoogleShopping\Helper\General as GeneralHelper;
use Magento\Backend\Block\Template\Context;

class Version extends Field
{

    /**
     * @var GeneralHelper
     */
    private $generalHelper;

    /**
     * Version constructor.
     *
     * @param Context       $context
     * @param GeneralHelper $generalHelper
     */
    public function __construct(
        Context $context,
        GeneralHelper $generalHelper
    ) {
        $this->generalHelper = $generalHelper;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function _getElementHtml(AbstractElement $element)
    {
        $html = $this->generalHelper->getExtensionVersion();
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
