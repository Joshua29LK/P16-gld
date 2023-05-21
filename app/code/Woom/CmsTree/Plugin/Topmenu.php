<?php

namespace Woom\CmsTree\Plugin;

use Magento\Theme\Block\Html\Topmenu as MagentoTopmenu;

class Topmenu
{
    /**
     * Add 'menu' cache tag, so we can later on clear menu cache when settings are updated
     *
     * @param MagentoTopmenu $subject
     *
     * @return void
     */
    public function beforeGetIdentities(MagentoTopmenu $subject)
    {
        $subject->addIdentity('menu');
    }
}
