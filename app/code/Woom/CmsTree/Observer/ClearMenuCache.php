<?php

namespace Woom\CmsTree\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\CacheInterface;

class ClearMenuCache implements ObserverInterface
{
    const MENU_SETTINGS = [
        'menu_add_category_id',
        'menu_add_type',
        'menu_label',
    ];

    /**
     * @var CacheInterface
     */
    private $cacheManager;

    /**
     * ClearMenuCache constructor.
     *
     * @param CacheInterface $cacheManager
     */
    public function __construct(
        CacheInterface $cacheManager
    ) {
        $this->cacheManager = $cacheManager;
    }

    /**
     * When menu settings are updated, clear menu cache
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $page = $observer->getEvent()->getObject();

        $shouldUpdateMenu = false;
        foreach (self::MENU_SETTINGS as $menuSetting) {
            if ($page->dataHasChangedFor($menuSetting)) {
                $shouldUpdateMenu = true;
                break;
            }
        }

        if ($shouldUpdateMenu) {
            $this->cacheManager->clean(['menu']);
        }
    }
}