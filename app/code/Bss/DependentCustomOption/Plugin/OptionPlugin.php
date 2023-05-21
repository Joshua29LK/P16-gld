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

class OptionPlugin
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Bss\DependentCustomOption\Model\DependOptionFactory
     */
    protected $dependOptionFactory;

    /**
     * @var \Bss\DependentCustomOption\Helper\ModuleConfig
     */
    protected $moduleConfig;

    /**
     * @var \Bss\DependentCustomOption\Helper\DependOption
     */
    protected $dependOption;

    /**
     * OptionPlugin constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Bss\DependentCustomOption\Model\DependOptionFactory $dependOptionFactory
     * @param \Bss\DependentCustomOption\Helper\ModuleConfig $moduleConfig
     * @param \Bss\DependentCustomOption\Helper\DependOption $dependOption
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Bss\DependentCustomOption\Model\DependOptionFactory $dependOptionFactory,
        \Bss\DependentCustomOption\Helper\ModuleConfig $moduleConfig,
        \Bss\DependentCustomOption\Helper\DependOption $dependOption
    ) {
        $this->logger = $logger;
        $this->dependOptionFactory = $dependOptionFactory;
        $this->moduleConfig = $moduleConfig;
        $this->dependOption = $dependOption;
    }

    /**
     * AfterSave
     *
     * @param Option $subject
     * @param Option $result
     * @return Option
     */
    public function afterSave(
        Option $subject,
        $result
    ) {
        if ($subject->getDependentId()) {
            $this->dependOption->saveDepend($subject, $result, 'option_id');
        }
        return $result;
    }

    /**
     * After GetData
     *
     * @param Option $subject
     * @param mixed $result
     * @param string $key
     * @param string $index
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetData(
        Option $subject,
        $result,
        $key = '',
        $index = null
    ) {
        if ($key === '') {
            if (!isset($result['dependent_id']) || !$result['dependent_id']) {
                if (isset($result['option_id'])) {
                    $dependData = $this->dependOptionFactory->create()->loadByOptionId($result['option_id']);
                    $result['dependent_id'] = $dependData ? $dependData->getData('increment_id') : null;
                } else {
                    $result['dependent_id'] = null;
                }
            }
        }
        if (($key === 'dependent_id') && !$subject->hasData('dependent_id')) {
            $dependData = $this->dependOptionFactory->create()->loadByOptionId($subject->getData('option_id'));
            return $dependData ? $dependData->getData('increment_id') : null;
        }
        return $result;
    }
}

