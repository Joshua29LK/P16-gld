<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Flags\Block\Adminhtml\Column\Edit\Tab;

use Amasty\Flags\Model\Column;
use Amasty\Flags\Model\Flag;
use Amasty\Flags\Model\ResourceModel\Flag\CollectionFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

class Apply extends Generic implements TabInterface
{
    /**
     * @var CollectionFactory
     */
    private $flagCollectionFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        CollectionFactory $flagCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->flagCollectionFactory = $flagCollectionFactory;
    }

    /**
     * @return string
     */
    public function getTabLabel(): string
    {
        return __('Apply Flags')->render();
    }

    /**
     * @return string
     */
    public function getTabTitle(): string
    {
        return __('Apply Flags')->render();
    }

    /**
     * @return bool
     */
    public function canShowTab(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return false;
    }

    /**
     * @return Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm(): Form
    {
        /** @var Column $model */
        $model = $this->_coreRegistry->registry('amflags_column');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('column_');
        $fieldset = $form->addFieldset('apply_fieldset', ['legend' => __('Apply Flags To Column')]);
        /** @var \Amasty\Flags\Model\ResourceModel\Flag\Collection $flags */
        $flags = $this->flagCollectionFactory->create();
        $flags->addOrder('priority', AbstractDb::SORT_ORDER_ASC);

        $values = [];
        /** @var Flag $flag */
        foreach ($flags as $flag) {
            $values[] = [
                'value' => $flag->getId(),
                'label' => $flag->getName()
            ];
        }

        $fieldset->addField('apply_flag', 'multiselect', [
            'name' => 'apply_flag',
            'label' => __('Flags'),
            'title' => __('Flags'),
            'values' => $values,
            'note' => __('Set flags to column'),
        ]);

        $model->setData('apply_flag', $model->getAppliedFlagIds());
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
