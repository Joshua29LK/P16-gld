<?php

namespace Woom\CmsTree\Block\Magento\Theme\Html;

use Magento\Theme\Block\Html\Breadcrumbs as MagentoBreadcrumbs;

class Breadcrumbs extends MagentoBreadcrumbs
{
    /**
     * Template file (Magento doesn't reference package name in block)
     *
     * @var string
     */
    protected $_template = 'Magento_Theme::html/breadcrumbs.phtml';

    /**
     * Get breadcrumbs
     *
     * @return array
     */
    public function getCrumbs()
    {
        return $this->_crumbs;
    }

    /**
     * Set breadcrumbs
     *
     * @param array $crumbs
     *
     * @return void
     */
    public function setCrumbs($crumbs)
    {
        $this->_crumbs = $crumbs;
    }
}
