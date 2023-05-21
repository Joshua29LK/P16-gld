<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

namespace Amasty\Flags\Block\Adminhtml\Flag\Edit\Tab;

use Amasty\Flags\Model\Flag;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class General extends Generic implements TabInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('General Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('General Information');
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

    protected function _prepareForm()
    {
        /** @var Flag $model */
        $model = $this->_coreRegistry->registry('amflags_flag');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('flag_');

        $fieldset = $form->addFieldset('general_fieldset', ['legend' => __('Flag Information')]);
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField('name', 'text', [
            'name'      => 'name',
            'required'  => true,
            'label'     => __('Name'),
            'title'     => __('Name'),
        ]);

        $fieldset->addField('image_name', 'file', [
            'label'    => __('Icon Image'),
            'name'     => 'image_name',
            'note'     => __(
                "JPG, PNG, GIF or SVG. 20x20 pixels strongly recommended. Images of different size will break design."
            ),
            'required' => !$model->getId(),

            'after_element_html' => $model->getId() ? "<img src=\"{$model->getImageUrl()}\" />" : '',
        ]);

        $fieldset->addField('priority', 'text', [
            'name'      => 'priority',
            'label'     => __('Priority'),
            'title'     => __('Priority'),
            'class'     => 'validate-number',
            'note'      => __('Numeric value for internal use.'),
        ]);

        $fieldset->addField('note', 'textarea', [
            'name'      => 'note',
            'label'     => __('Note'),
            'title'     => __('Note')
        ]);

        $form->setValues($model->getData());

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
