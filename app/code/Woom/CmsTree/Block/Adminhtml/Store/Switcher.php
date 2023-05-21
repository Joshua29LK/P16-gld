<?php

namespace Woom\CmsTree\Block\Adminhtml\Store;

use Magento\Backend\Block\Store\Switcher as MagentoSwitcher;

class Switcher extends MagentoSwitcher
{
    /**
     * Determine if store switcher should be shown
     * Hide if store has only one store view
     *
     * @return bool
     */
    public function isShow()
    {
        return parent::isShow() && !$this->_storeManager->hasSingleStore();
    }
}
