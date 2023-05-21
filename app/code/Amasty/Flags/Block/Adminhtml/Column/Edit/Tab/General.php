<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

namespace Amasty\Flags\Block\Adminhtml\Column\Edit\Tab;

use Amasty\Flags\Model\Column;
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
        /** @var Column $model */
        $model = $this->_coreRegistry->registry('amflags_column');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('column_');

        $fieldset = $form->addFieldset('general_fieldset', ['legend' => __('Column Information')]);
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField('name', 'text', [
            'name'      => 'name',
            'required'  => true,
            'label'     => __('Name'),
            'title'     => __('Name'),
        ]);

        $fieldset->addField('position', 'text', [
            'name'      => 'position',
            'label'     => __('Position'),
            'title'     => __('Position'),
            'class'     => 'validate-number',
            'note'      => __('Numeric value for internal use.'),
        ]);

        $fieldset->addField('comment', 'textarea', [
            'name'      => 'comment',
            'label'     => __('Comments'),
            'title'     => __('Comments')
        ]);

        $form->setValues($model->getData());

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
