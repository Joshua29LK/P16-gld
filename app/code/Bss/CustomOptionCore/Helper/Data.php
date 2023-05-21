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
 * @package    Bss_CustomOptionCore
 * @author     Extension Team
 * @copyright  Copyright (c) 2020-2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CustomOptionCore\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        parent::__construct($context);
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param string $moduleName
     * @return bool
     */
    public function isModuleInstall($moduleName)
    {
        if ($this->moduleManager->isEnabled($moduleName)) {
            return true;
        }
        return false;
    }

    /**
     * @param string $moduleName
     * return string
     * @codingStandardsIgnoreStart
     */
    public function getCommentInformationModule($moduleName)
    {
        switch ($moduleName) {
            case 'Bss_CustomOptionImage':
                return __('Add images to custom options.') . '</br><a target="_blank" href="https://bsscommerce.com/magento-2-custom-option-image-extension.html">' . __('Check now!') . '</a>';
            case 'Bss_CustomOptionAbsolutePriceQuantity':
                return __('Add absolute price and qty to custom options.') . '</br><a target="_blank" href="https://bsscommerce.com/magento-2-custom-option-absolute-price-and-quantity-extension.html">' . __('Check now!') . '</a>';
            case 'Bss_DependentCustomOption':
                return __('Create the relationship between custom options.') . '</br><a target="_blank" href="https://bsscommerce.com/magento-2-dependent-custom-options-extension.html">' . __('Check now!') . '</a>';
            case 'Bss_CustomOptionTemplate':
                return __('Setup various templates and assign them to hundreds of products.') . '</br><a target="_blank" href="https://bsscommerce.com/magento-2-custom-option-template-extension.html">' . __('Check now!') . '</a>';
        }
        return 'Bss_CustomOptionCore';
    }
}
