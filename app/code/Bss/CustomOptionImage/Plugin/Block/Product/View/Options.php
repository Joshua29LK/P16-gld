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
 * @package    Bss_CustomOptionImage
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\CustomOptionImage\Plugin\Block\Product\View;

use Magento\Catalog\Model\Product\Option;

class Options
{
    /**
     * @var \Bss\CustomOptionImage\Helper\ModuleConfig
     */
    protected $moduleConfig;

    /**
     * Options constructor.
     * @param \Bss\CustomOptionImage\Helper\ModuleConfig $moduleConfig
     */
    public function __construct(
        \Bss\CustomOptionImage\Helper\ModuleConfig $moduleConfig
    ) {
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * Around OptionHTMl
     *
     * @param \Magento\Catalog\Block\Product\View\Options $subject
     * @param mixed $result
     * @param Option $option
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetOptionHtml(
        \Magento\Catalog\Block\Product\View\Options $subject,
        $result,
        \Magento\Catalog\Model\Product\Option $option
    ) {
        $typeOption = [
            Option::OPTION_TYPE_DROP_DOWN,
            Option::OPTION_TYPE_RADIO,
            Option::OPTION_TYPE_CHECKBOX,
            Option::OPTION_TYPE_MULTIPLE
        ];
        if ($this->moduleConfig->isModuleEnable() && in_array($option->getType(), $typeOption)) {
            $block = $subject->getLayout()->createBlock(\Bss\CustomOptionImage\Block\Render\PluginBlock::class);
            $block->setProduct($subject->getProduct())->setOption($option);
            $output = $block->toHtml();
            return $output = str_replace('<div class="control">', $output . '<div class="control">', $result);
        }
        return $result;
    }
}
