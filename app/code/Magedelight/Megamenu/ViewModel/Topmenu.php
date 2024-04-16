<?php

namespace Magedelight\Megamenu\ViewModel;

use Magedelight\Megamenu\Api\Data\CacheInterfaceFactory;
use Magedelight\Megamenu\Helper\Cache;
use Magedelight\Megamenu\Model\CacheRepository;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Topmenu implements ArgumentInterface
{

    private Cache $cacheHelper;
    private CacheInterfaceFactory $cacheFactory;
    private CacheRepository $cacheRepository;

    /**
     * @param Cache $cacheHelper
     * @param CacheInterfaceFactory $cacheFactory
     * @param CacheRepository $cacheRepository
     */
    public function __construct(
        Cache $cacheHelper,
        CacheInterfaceFactory $cacheFactory,
        CacheRepository $cacheRepository
    ) {
        $this->cacheHelper = $cacheHelper;
        $this->cacheFactory = $cacheFactory;
        $this->cacheRepository = $cacheRepository;
    }

    /**
     * @return int
     */
    public function getStoreId(): int
    {
        return $this->cacheHelper->getStoreId();
    }

    /**
     * @return mixed
     */
    public function enableCacheMenu()
    {
        return $this->cacheHelper->enableCustomMenu();
    }

    /**
     * @return string
     */
    public function getStoreMenuKey(): string
    {
        return $this->cacheHelper->getStoreMenuKey().'_'.$this->getStoreId();
    }

    /**
     * @return mixed
     */
    public function getMenuData()
    {
        return $this->cacheHelper->getCustomVariable($this->getStoreMenuKey());
    }

    /**
     * @param $variableCode
     * @param $fileContent
     * @param $storeId
     * @return void
     */
    public function saveVariableByCode($variableCode, $fileContent)
    {
        $cacheData = $this->cacheRepository->loadByName($variableCode);
        if (sizeof($cacheData->getData()) > 0) {
            $cacheModel = $this->cacheRepository->get($cacheData->getCacheId());
            $cacheModel->setHtmlValue($fileContent);
        } else {
            $cacheModel = $this->cacheFactory->create();
            $data = [
                'name' => $variableCode,
                'html_value' => $fileContent,
                'store_id' => $this->getStoreId()
            ];

            $cacheModel->setData($data);
        }

        $this->cacheRepository->save($cacheModel);
    }
}
