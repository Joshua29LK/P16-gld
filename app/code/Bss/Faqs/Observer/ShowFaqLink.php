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
 * @package    Bss_Faqs
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\Faqs\Observer;

use Magento\Framework\Event\ObserverInterface;

class ShowFaqLink implements ObserverInterface
{
    /**
     * @var \Bss\Faqs\Helper\ModuleConfig
     */
    private $moduleConfig;

    /**
     * ShowFaqLink constructor.
     * @param \Bss\Faqs\Helper\ModuleConfig $moduleConfig
     */
    public function __construct(
        \Bss\Faqs\Helper\ModuleConfig $moduleConfig
    ) {
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->moduleConfig->isModuleEnable()) {
            $layout = $observer->getData('layout');
            $layout->getUpdate()->addHandle('show_link');
            if ($this->moduleConfig->isAddRoboto()) {
                $layout->getUpdate()->addHandle('add_font_roboto');
            }
            if ($this->moduleConfig->isAddAwesome()) {
                $layout->getUpdate()->addHandle('add_font_awesome');
            }
        }
        return $this;
    }
}
