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

class Header extends Field
{

    public const MODULE_CODE = 'googleshopping-magento2';
    public const MODULE_SUPPORT_LINK = 'https://www.magmodules.eu/help/' . self::MODULE_CODE;
    public const MODULE_CONTACT_LINK = 'https://www.magmodules.eu/support.html?ext=' . self::MODULE_CODE;

    /**
     * @var string
     */
    protected $_template = 'Magmodules_GoogleShopping::system/config/header.phtml';
    /**
     * @var GeneralHelper
     */
    private $generalHelper;

    /**
     * Header constructor.
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
    public function render(AbstractElement $element)
    {
        $element->addClass('magmodules');

        return $this->toHtml();
    }

    /**
     * Image with extension and magento version
     *
     * @return string
     */
    public function getImage(): string
    {
        return sprintf(
            'https://www.magmodules.eu/logo/%s/%s/%s/logo.png',
            self::MODULE_CODE,
            $this->generalHelper->getExtensionVersion(),
            $this->generalHelper->getMagentoVersion()
        );
    }

    /**
     * Contact link for extension
     *
     * @return string
     */
    public function getContactLink(): string
    {
        return self::MODULE_CONTACT_LINK;
    }

    /**
     * Support link for extension
     *
     * @return string
     */
    public function getSupportLink(): string
    {
        return self::MODULE_SUPPORT_LINK;
    }
}
