<?php

namespace Woom\CmsTree\Block\Adminhtml\Page;

use Magento\Framework\View\Element\Template;

class Edit extends Template
{
    /**
     * Return URL for refresh input element 'path' in form
     * Used after page move
     *
     * @return string
     */
    public function getRefreshPathUrl()
    {
        return $this->getUrl('cmstree/*/refreshPath', ['_current' => true]);
    }
}
