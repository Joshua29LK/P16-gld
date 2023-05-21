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
namespace Bss\DependentCustomOption\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class ModuleConfig extends AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var integer
     */
    protected $storeId;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * ModuleConfig constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        $this->storeManager = $storeManager;
        $this->productMetadata = $productMetadata;
        parent::__construct($context);
    }

    /**
     * GetStoreId
     *
     * @return int
     * @throws NoSuchEntityException
     */
    public function getStoreId()
    {
        if ($this->storeId === null) {
            $this->storeId = $this->storeManager->getStore()->getId();
        }
        return $this->storeId;
    }

    /**
     * IsModuleEnable
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isModuleEnable()
    {
        return $this->scopeConfig->getValue(
            'dependent_co/general/enable',
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    /**
     * GetChildrenDisplay
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getChildrenDisplay()
    {
        return $this->scopeConfig->getValue(
            'dependent_co/general/children_display',
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    /**
     * GetMultipleParentValue
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMultipleParentValue()
    {
        return $this->scopeConfig->getValue(
            'dependent_co/general/multiple_parent',
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    /**
     * Check edition version M2
     *
     * @return bool
     */
    public function checkVersion()
    {
        if ($this->productMetadata->getEdition() == 'Enterprise' || $this->productMetadata->getEdition() == 'B2B') {
            return true;
        }
        return false;
    }
}