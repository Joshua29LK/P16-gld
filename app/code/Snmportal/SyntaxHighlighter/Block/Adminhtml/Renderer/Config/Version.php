<?php

namespace Snmportal\SyntaxHighlighter\Block\Adminhtml\Renderer\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Version
 * @package Snmportal\SyntaxHighlighter\Block\Adminhtml\Renderer\Config
 */
class Version extends Field
{


    /**
     * @var \Snmportal\SyntaxHighlighter\Helper\Base
     */
    protected $baseHelper;

    /**
     * Version constructor.
     * @param \Snmportal\SyntaxHighlighter\Helper\Base $baseHelper
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Snmportal\SyntaxHighlighter\Helper\Base $baseHelper,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->baseHelper = $baseHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $dVersion = $this->baseHelper->getModulVersion();
        $info = $this->baseHelper->Info();
        $html = '<div style="padding-top:7px">';
        $html .= '<div >' . $dVersion . '&#160;(<span>' . $this->baseHelper->getServerName() . '</span>)</div>';
        if ($info) {
            if (version_compare($info['version'], $dVersion) > 0) {
                $html .= '<b>' . __('New version %1 available on the server.', $info['version']) . '</b>';
            } else {
                $html .= '<b>' . __('is current version.') . '</b>';
            }
            if (isset($info['msg'])) {
                $html .= '<br/> ' . $info['msg'] . '';
            }
        }
        $html .= '</div>';
        return $html;
    }
}
