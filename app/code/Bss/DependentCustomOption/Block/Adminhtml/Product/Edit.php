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
namespace Bss\DependentCustomOption\Block\Adminhtml\Product;

class Edit extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Bss\DependentCustomOption\Helper\ModuleConfig
     */
    protected $moduleConfig;

    /**
     * @var \Bss\DependentCustomOption\Model\DependOptionFactory
     */
    protected $dependOptionFactory;

    /**
     * Edit constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Bss\DependentCustomOption\Helper\ModuleConfig $moduleConfig
     * @param \Bss\DependentCustomOption\Model\DependOptionFactory $dependOptionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Bss\DependentCustomOption\Helper\ModuleConfig $moduleConfig,
        \Bss\DependentCustomOption\Model\DependOptionFactory $dependOptionFactory,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->moduleConfig = $moduleConfig;
        $this->dependOptionFactory = $dependOptionFactory;
        parent::__construct($context, $data);
    }

    /**
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('current_product');
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getLastIncrementId()
    {
        $productId = $this->getProduct()->getId();
        if ($this->moduleConfig->checkVersion()) {
            $productId = $this->getProduct()->getRowId();
        }
        $lastIncrementId = $this->dependOptionFactory->create()->getLastIncrementId($productId);
        return (int)$lastIncrementId;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isModuleEnable()
    {
        return $this->moduleConfig->isModuleEnable();
    }
}