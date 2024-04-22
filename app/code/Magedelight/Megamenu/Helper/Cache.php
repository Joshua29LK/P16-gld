<?php

namespace Magedelight\Megamenu\Helper;

use Magedelight\Megamenu\Api\Data\CacheInterfaceFactory;
use Magedelight\Megamenu\Model\CacheRepository;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Variable\Model\VariableFactory;

/**
 * Class Data
 * @data Magedelight\Megamenu\Helper
 */
class Cache extends AbstractHelper
{
    protected $variable;
    protected $varFactory;
    protected $storeManager;

    const STORE_MENU = 'store_menu';
    const SHORTCODE_MENU = 'shortcode_menu';
    private CacheInterfaceFactory $cacheFactory;
    private CacheRepository $cacheRepository;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param VariableFactory $varFactory
     * @param CacheInterfaceFactory $cacheFactory
     * @param CacheRepository $cacheRepository
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        VariableFactory $varFactory,
        CacheInterfaceFactory $cacheFactory,
        CacheRepository $cacheRepository
    ) {
        $this->storeManager = $storeManager;
        $this->varFactory = $varFactory;
        parent::__construct($context);
        $this->cacheFactory = $cacheFactory;
        $this->cacheRepository = $cacheRepository;
    }

    public function getCustomVariable($variableCode)
    {
        $cacheData = $this->cacheRepository->loadByName($variableCode);
        return $cacheData->getHtmlValue();
    }

    /**
     * @param $variableCode
     * @return void
     * @throws \Exception
     */
    public function updateVariableByCode($variableCode)
    {
        $allStoreIds = array_keys($this->storeManager->getStores(true));
        foreach ($allStoreIds as $storeId) {
            $readVariableCode = $variableCode.'_'.$storeId;
            $cacheData = $this->cacheRepository->loadByName($readVariableCode);
            if (sizeof($cacheData->getData()) > 0) {
                $cacheModel = $this->cacheRepository->get($cacheData->getCacheId());
                $cacheModel->setHtmlValue('');
                $this->cacheRepository->save($cacheModel);
            }
        }
    }


    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    public function enableCustomMenu()
    {
        return $this->scopeConfig->getValue(
            'magedelight/general/menu_custom_enabled',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getStoreMenuId()
    {
        return $this->scopeConfig->getValue(
            'magedelight/general/primary_menu',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getStoreMenuKey()
    {
        return self::STORE_MENU;
    }
}
