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
namespace Bss\Faqs\Block;

class ModuleBlock extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Bss\Faqs\Helper\ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var \Bss\Faqs\Model\Config\Source\FaqSortBy
     */
    private $faqSortBy;

    /**
     * ModuleBlock constructor.
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Bss\Faqs\Model\Config\Source\FaqSortBy $faqSortBy
     * @param \Bss\Faqs\Helper\ModuleConfig $moduleConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Element\Template\Context $context,
        \Bss\Faqs\Model\Config\Source\FaqSortBy $faqSortBy,
        \Bss\Faqs\Helper\ModuleConfig $moduleConfig,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->moduleConfig = $moduleConfig;
        $this->faqSortBy = $faqSortBy;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Get module config
     *
     * @return \Bss\Faqs\Helper\ModuleConfig
     */
    public function getModuleConfig()
    {
        return $this->moduleConfig;
    }

    /**
     * Get sort by config
     *
     * @return array
     */
    public function getSortByConfig()
    {
        return $this->faqSortBy->toOptionArray();
    }

    /**
     * Get registry data
     *
     * @param string $key
     * @return mixed
     */
    public function getRegistryData($key)
    {
        return $this->coreRegistry->registry($key);
    }
}
