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
namespace Bss\DependentCustomOption\Plugin;

use Magento\Catalog\Model\Product\Option;

class Product
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Bss\DependentCustomOption\Helper\ModuleConfig
     */
    protected $moduleConfig;

    /**
     * OptionPlugin constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Bss\DependentCustomOption\Helper\ModuleConfig $moduleConfig
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Bss\DependentCustomOption\Helper\ModuleConfig $moduleConfig
    ) {
        $this->logger = $logger;
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * AfterSave
     *
     * @param Option $subject
     * @param Option $result
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeCheckProductBuyState($subject, $product)
    {
        if ($this->moduleConfig->isModuleEnable()) {
            $product->setSkipCheckRequiredOption(true);
        }
        return [$product];
    }
}
