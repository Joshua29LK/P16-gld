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

class ProductOption
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
     * @param \Bss\DependentCustomOption\Model\DependOptionFactory $dependOptionFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Bss\DependentCustomOption\Helper\ModuleConfig $moduleConfig,
        \Bss\DependentCustomOption\Model\DependOptionFactory $dependOptionFactory
    ) {
        $this->logger = $logger;
        $this->moduleConfig = $moduleConfig;
        $this->dependOptionFactory = $dependOptionFactory;
    }

    /**
     * AfterSave
     *
     * @param Option $subject
     * @param Option $result
     * @return Option
     */
    public function beforeValidateUserValue($subject, $values)
    {
        if ($this->moduleConfig->isModuleEnable()) {
            $option = $subject->getOption();
            $co = $this->dependOptionFactory->create()->getCollection()
                ->addFieldToFilter('product_id', $option->getProductId());
            $depend_value = $co->addFieldToFilter('depend_value', ['notnull' => true])->getData();
            $array = [];
            foreach ($depend_value as $value) {
                $array[] = $value['depend_value'];
            }
            if ($option->getIsRequire() && in_array($option->getDependentId(), $array)) {
                $option->setIsRequire(false);
            }
        }
        return [$values];
    }
}
