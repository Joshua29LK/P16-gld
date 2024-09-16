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
 * @category   BSS
 * @package    Bss_Faqs
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\Faqs\Block\Adminhtml\Faqs;

use Magento\Framework\Phrase;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Construct
     *
     * @return void
     */
    public function _construct()
    {
        $this->_objectId = 'faq_id';
        $this->_blockGroup = 'Bss_Faqs';
        $this->_controller = 'adminhtml_faqs';

        parent::_construct();

        if ($this->_isAllowedAction('Bss_Faqs::faqs_save')) {
            $this->buttonList->update('save', 'label', __('Save FAQ'));
        } else {
            $this->buttonList->remove('save');
        }

        if ($this->_isAllowedAction('Bss_Faqs::faqs_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * Get header text
     *
     * @return Phrase|string
     */
    public function getHeaderText()
    {
        if ($this->coreRegistry->registry('faq_id')) {
            return __("Edit FAQ ID: %1", $this->escapeHtml($this->coreRegistry->registry('faq_id')));
        } else {
            return __('New FAQ');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    public function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
