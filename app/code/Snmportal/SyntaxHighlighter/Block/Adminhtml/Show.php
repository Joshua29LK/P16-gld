<?php
/*
* Copyright © 2016 SNM-Portal.com. All rights reserved.
* See LICENSE.txt for license details.
*/
namespace Snmportal\SyntaxHighlighter\Block\Adminhtml;

/**
 * Form fieldset renderer
 */
use Magento\Backend\Block\Template;
use Magento\Store\Model\ScopeInterface;
use Snmportal\SyntaxHighlighter\Plugin\Cms\Model\Wysiwyg\Config;

/**
 * Class Show
 * @package Snmportal\SyntaxHighlighter\Block\Adminhtml
 */
class Show extends Template
{

    /**
     * @var string
     */
    protected $_template = 'show.phtml';

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        if ($this->isEnabled()) {
            /*
            $this->pageConfig->addPageAsset('Snmportal_SyntaxHighlighter/cm/lib/codemirror.css');
            $this->pageConfig->addPageAsset('Snmportal_SyntaxHighlighter/cm/addon/hint/show-hint.css');
            $this->pageConfig->addPageAsset('Snmportal_SyntaxHighlighter/cm/addon/dialog/dialog.css');
            $this->pageConfig->addPageAsset('Snmportal_SyntaxHighlighter/cm/lib/snm.css');
            */
        }
        return parent::_prepareLayout();
    }

    /**
     * @return array|mixed
     */
    public function getElements()
    {
        $value = $this->_scopeConfig->getValue(
            Config::BGELEMENTS,
            ScopeInterface::SCOPE_STORE);
        $value = json_decode($value, true);
        if (is_array($value)) {
            return $value;
        }
        return [];
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_scopeConfig->isSetFlag(Config::ENABLED,
            ScopeInterface::SCOPE_STORE);
    }
    public function getJsonOption()
    {
        $option = array();
        $option['lineWrapping'] = (int)$this->_scopeConfig->getValue('snmportal_syntaxhighlighter/general/line_wrapping',
            ScopeInterface::SCOPE_STORE);
        return json_encode($option);

    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->isEnabled()) {
            return parent::_toHtml();
        }
        return '';
    }

}
