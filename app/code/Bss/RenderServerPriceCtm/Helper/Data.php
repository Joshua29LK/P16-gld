<?php
namespace Bss\RenderServerPriceCtm\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return mixed
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getStoreId();
    }

    /**
     * @param $currentUrl
     * @return bool
     */
    public function isChild($currentUrl = null)
    {
        if (!$currentUrl) {
            $currentUrl = $this->urlBuilder->getCurrentUrl();
        }
        if (strpos($currentUrl, '~') !== false || strpos($currentUrl, '%7E') !== false) {
            return true;
        }
        return false;
    }

    /**
     * @return false|string
     */
    public function getChildParams($currentUrl = null)
    {
        if (!$currentUrl) {
            $urlWithParams = $this->urlBuilder->getCurrentUrl();
        } else {
            $urlWithParams = str_replace($currentUrl, '%7E', '~');
            $urlWithParams = str_replace($urlWithParams, '%3D', '=');
        }
        $selectedParams = explode('~', $urlWithParams);
        if (isset($selectedParams[1])) {
            return $selectedParams[1];
        }
        return false;
    }
}