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

namespace Bss\CustomOptionImage\Helper;

use Magento\Catalog\Model\Product\Option;

class ModuleConfig extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var int
     */
    protected $storeId;

    /**
     * ModuleConfig constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isModuleEnable()
    {
        return $this->scopeConfig->getValue(
            'bss_coi/general/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCheckboxSizeX()
    {
        $size = $this->scopeConfig->getValue(
            'bss_coi/image_size/checkbox_x',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
        return ($size === null) ? 50 : (int)$size;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCheckboxSizeY()
    {
        $size = $this->scopeConfig->getValue(
            'bss_coi/image_size/checkbox_y',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
        return ($size === null) ? 50 : (int)$size;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRadioSizeX()
    {
        $size = $this->scopeConfig->getValue(
            'bss_coi/image_size/radio_x',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
        return ($size === null) ? 50 : (int)$size;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRadioSizeY()
    {
        $size = $this->scopeConfig->getValue(
            'bss_coi/image_size/radio_y',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
        return ($size === null) ? 50 : (int)$size;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMultipleSizeX()
    {
        $size = $this->scopeConfig->getValue(
            'bss_coi/image_size/multiple_x',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
        return ($size === null) ? 40 : (int)$size;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMultipleSizeY()
    {
        $size = $this->scopeConfig->getValue(
            'bss_coi/image_size/multiple_y',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
        return ($size === null) ? 40 : (int)$size;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDropdownSizeX()
    {
        $size = $this->scopeConfig->getValue(
            'bss_coi/image_size/dropdown_x',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
        return ($size === null) ? 60 : (int)$size;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDropdownSizeY()
    {
        $size = $this->scopeConfig->getValue(
            'bss_coi/image_size/dropdown_y',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
        return ($size === null) ? 60 : (int)$size;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDropdownView()
    {
        $config = $this->scopeConfig->getValue(
            'bss_coi/frontend_view/dropdown',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
        return ($config === null) ? 0 : (int)$config;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMultipleSelectView()
    {
        $config = $this->scopeConfig->getValue(
            'bss_coi/frontend_view/multiple',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
        return ($config === null) ? 0 : (int)$config;
    }

    /**
     * @param string $type
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageY($type)
    {
        switch ($type) {
            case Option::OPTION_TYPE_DROP_DOWN:
                return $this->getDropdownSizeY();

            case Option::OPTION_TYPE_MULTIPLE:
                return $this->getMultipleSizeY();

            case Option::OPTION_TYPE_RADIO:
                return $this->getRadioSizeY();

            case Option::OPTION_TYPE_CHECKBOX:
                return $this->getCheckboxSizeY();
        }

        return false;
    }

    /**
     * @param string $type
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageX($type)
    {
        switch ($type) {
            case Option::OPTION_TYPE_DROP_DOWN:
                return $this->getDropdownSizeX();

            case Option::OPTION_TYPE_MULTIPLE:
                return $this->getMultipleSizeX();

            case Option::OPTION_TYPE_RADIO:
                return $this->getRadioSizeX();

            case Option::OPTION_TYPE_CHECKBOX:
                return $this->getCheckboxSizeX();
        }

        return false;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        if ($this->storeId === null) {
            $this->storeId = $this->storeManager->getStore()->getId();
        }
        return $this->storeId;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPriceDisplaySetting()
    {
        return $this->scopeConfig->getValue(
            'tax/display/type',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }
}
