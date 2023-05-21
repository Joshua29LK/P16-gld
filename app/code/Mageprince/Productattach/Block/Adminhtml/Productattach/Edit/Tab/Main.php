<?php

/**
 * MagePrince
 * Copyright (C) 2020 Mageprince <info@mageprince.com>
 *
 * @package Mageprince_Productattach
 * @copyright Copyright (c) 2020 Mageprince (http://www.mageprince.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MagePrince <info@mageprince.com>
 */

namespace Mageprince\Productattach\Block\Adminhtml\Productattach\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Exception\LocalizedException;
use Mageprince\Productattach\Block\Adminhtml\Productattach\Renderer\FileIconAdmin;

class Main extends Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    protected $customerCollection;

    /**
     * @var \Magento\Framework\File\Size
     */
    protected $fileSize;

    /**
     * @var \Mageprince\Productattach\Model\Config\Source\FileType
     */
    protected $fileTypeConfig;

    /**
     * Main constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection $customerCollection
     * @param \Magento\Framework\File\Size $fileSize
     * @param \Mageprince\Productattach\Model\Config\Source\FileType $fileTypeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerCollection,
        \Magento\Framework\File\Size $fileSize,
        \Mageprince\Productattach\Model\Config\Source\FileType $fileTypeConfig,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->customerCollection = $customerCollection;
        $this->fileSize = $fileSize;
        $this->fileTypeConfig = $fileTypeConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return Main
     * @throws LocalizedException
     */
    public function _prepareForm()
    {

        $model = $this->_coreRegistry->registry('productattach');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Mageprince_Productattach::manage')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('productattach_main_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Attachment Information')]
        );

        $customerGroup = $this->customerCollection->toOptionArray();

        if ($model->getId()) {
            $fieldset->addField('productattach_id', 'hidden', ['name' => 'productattach_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Attachment Name'),
                'title' => __('Attachment Name'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'disabled' => $isElementDisabled
            ]
        );

        $uploadFile = $fieldset->addField(
            'files',
            'file',
            [
                'name' => 'file',
                'label' => __('Upload File'),
                'title' => __('Upload File'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $uploadedFile = $fieldset->addType(
            'uploadedfile',
            FileIconAdmin::class
        );

        $fieldset->addField(
            'file',
            'uploadedfile',
            [
                'name'  => 'uploadedfile',
                'label' => __('Uploaded File'),
                'title' => __('Uploaded File'),

            ]
        );

        $urlField = $fieldset->addField(
            'url',
            'text',
            [
                'name' => 'url',
                'label' => __('URL'),
                'title' => __('URL'),
                'required' => false,
                'disabled' => $isElementDisabled,
                'note' => 'Upload file or Enter url'
            ]
        );

        $fieldset->addField(
            'customer_group',
            'multiselect',
            [
                'name' => 'customer_group[]',
                'label' => __('Customer Group'),
                'title' => __('Customer Group'),
                'required' => true,
                'value' => [0,1,2,3], // todo: preselect ALL customer groups, not just 0-3
                'values' => $customerGroup,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'store',
            'multiselect',
            [
                'name' => 'store[]',
                'label' => __('Store'),
                'title' => __('Store'),
                'required' => true,
                'value' => [0],
                'values' => $this->systemStore->getStoreValuesForForm(false, true),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'active',
            'select',
            [
                'name' => 'active',
                'label' => __('Active'),
                'title' => __('Active'),
                'value' => 1,
                'options' => ['1' => __('Yes'), '0' => __('No')],
                'disabled' => $isElementDisabled
            ]
        );

        $this->_eventManager->dispatch('adminhtml_productattach_edit_tab_main_prepare_form', ['form' => $form]);

        if ($model->getId()) {
            $form->setValues($model->getData());
        }

        $fieldset->addField(
            'max_file_size',
            'hidden',
            [
                'name' => 'max_file_size',
                'value' => $this->fileSize->getMaxFileSize()
            ]
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Attachment Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Attachment Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
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
