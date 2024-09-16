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
namespace Bss\Faqs\Block\Adminhtml\FaqCategory;

use Magento\Framework\Registry;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var Registry
     */
    private $coreRegistry = null;

    /**
     * Class Constructor
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
        $this->_objectId = 'faq_category_id';
        $this->_blockGroup = 'Bss_Faqs';
        $this->_controller = 'adminhtml_faqCategory';

        parent::_construct();

        if ($this->_isAllowedAction('Bss_Faqs::faqCategory_save')) {
            $this->buttonList->update('save', 'label', __('Save Category'));
        } else {
            $this->buttonList->remove('save');
        }

        if ($this->_isAllowedAction('Bss_Faqs::faqCategory_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded post
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->coreRegistry->registry('faq_category_id')) {
            return __("Edit Category ID: %1", $this->escapeHtml($this->coreRegistry->registry('faq_category_id')));
        } else {
            return __('New Category');
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
