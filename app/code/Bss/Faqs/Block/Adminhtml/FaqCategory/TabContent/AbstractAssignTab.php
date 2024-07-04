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
namespace Bss\Faqs\Block\Adminhtml\FaqCategory\TabContent;

use Magento\Backend\Block\Widget\Form\Generic;

class AbstractAssignTab extends Generic
{
    /**
     * @var \Magento\Backend\Helper\Data|null
     */
    protected $adminhtmlData = null;

    /**
     * @var \Bss\Faqs\Model\FaqsFactory
     */
    protected $faqCategoryFactory;

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $optionYesnoProvider;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;

    /**
     * @var \Bss\Faqs\Block\Adminhtml\FaqCategory\Edit\CategoryImageBlock
     */
    protected $imagePreview;

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Config\Model\Config\Source\Yesno $optionYesnoProvider
     * @param \Bss\Faqs\Model\FaqCategoryFactory $faqCategoryFactory
     * @param \Bss\Faqs\Block\Adminhtml\FaqCategory\Edit\CategoryImageBlock $imagePreview
     * @param \Magento\Backend\Helper\Data $adminhtmlData
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Yesno $optionYesnoProvider,
        \Bss\Faqs\Model\FaqCategoryFactory $faqCategoryFactory,
        \Bss\Faqs\Block\Adminhtml\FaqCategory\Edit\CategoryImageBlock $imagePreview,
        \Magento\Backend\Helper\Data $adminhtmlData,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->optionYesnoProvider = $optionYesnoProvider;
        $this->faqCategoryFactory = $faqCategoryFactory;
        $this->adminhtmlData = $adminhtmlData;
        $this->wysiwygConfig = $wysiwygConfig;
        $this->imagePreview = $imagePreview;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Get Image preview block
     *
     * @return string
     */
    public function getPreviewBlock()
    {
        $this->imagePreview->setCategoryImage($this->getModel()->getData('image'));
        $this->imagePreview->setTemplate('Bss_Faqs::category_image_template.phtml');
        return $this->imagePreview->toHtml();
    }

    /**
     * Get Yes/no provider
     *
     * @return array
     */
    public function getOptionYesnoProvider()
    {
        return $this->optionYesnoProvider->toArray();
    }

    /**
     * Get wysiwyg config
     *
     * @return \Magento\Framework\DataObject
     */
    public function getWysiwygConfig()
    {
        return $this->wysiwygConfig->getConfig();
    }

    /**
     * Get Faq category model
     *
     * @return \Bss\Faqs\Model\FaqCategory
     */
    public function getModel()
    {
        if ($this->_coreRegistry->registry('faq_category_id') == 0) {
            $this->setCategoryData($this->faqCategoryFactory->create());
        }
        return $this->faqCategoryFactory->create()->load($this->_coreRegistry->registry('faq_category_id'));
    }
}
