<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category  BSS
 * @package   Bss_CustomOptionCore
 * @author    Extension Team
 * @copyright Copyright (c) 2020-2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\CustomOptionCore\Block\Adminhtml\Module;

class Base extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var string
     */
    protected $module;

    /**
     * @var \Bss\CustomOptionCore\Helper\Data
     */
    protected $dataHelper;

    /**
     * Base constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Bss\CustomOptionCore\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Bss\CustomOptionCore\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _renderValue(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '<td class="value">';
        if ($this->dataHelper->isModuleInstall($this->module)) {
            $html .= '<p style="color:#008000">' . __('Installed') . '</p>';
        } else {
            $html .= '<p style="color:#ff0000;font-weight: bold;">' . __('Not Installed') . '</p>';
        }
        $html .= '<p class="note">';
        $html.= '<span>' . $this->dataHelper->getCommentInformationModule($this->module) . '</span></p>';
        $html .= '</td>';
        return $html;
    }
}
