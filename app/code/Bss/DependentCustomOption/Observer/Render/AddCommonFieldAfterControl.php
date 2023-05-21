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
namespace Bss\DependentCustomOption\Observer\Render;

use Bss\DependentCustomOption\Block\Render\CommonFieldDependentControl;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class AddCommonFieldAfterControl implements ObserverInterface
{
    /**
     * Execute
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $observer->getChild()->addData(
            ['dco_common_control' => CommonFieldDependentControl::class]
        );
    }
}
