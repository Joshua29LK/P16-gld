<?php
namespace Bss\CustomizeDeliveryDate\Block\Adminhtml\Group\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Registry;
use Bss\CustomizeDeliveryDate\Model\ZipDeliveryFactory;

class Form extends Generic
{
    /**
     * Zip Delivery Factory
     *
     * @var ZipDeliveryFactory
     */
    protected $zipFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param ZipFactory $zipFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        ZipDeliveryFactory $zipFactory,
        array $data = []
    ) {
        $this->zipFactory = $zipFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $zipId = $this->_coreRegistry->registry('zip_id');

        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getUrl('*/*/save'), 'method' => 'post']]
        );

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Zip Delivery Information')]);

        if ($zipId) {
            $fieldset->addField(
                'zip_id',
                'hidden',
                ['name' => 'zip_id']
            );
        }

        $fieldset->addField(
            'zip_is_range',
            'checkbox',
            [
                'name' => 'zip_is_range',
                'label' => __('Zip/Post is Range'),
                'title' => __('Zip/Post is Range'),
                'onclick' => 'toggleRangeFields(this)',
                'checked' => false,
                'value' => 1,
            ]
        );
        
        $fieldset->addField(
            'zip_code',
            'text',
            [
                'name' => 'zip_code',
                'label' => __('Zip/Post Code'),
                'title' => __('Zip/Post Code'),
                'note' => __("'*' - matches any; 'xyz*' - matches any that begins on 'xyz' and are not longer than 10."),
                'required' => false
            ]
        );

        $fieldset->addField(
            'range_from',
            'text',
            [
                'name' => 'range_from',
                'label' => __('Range From'),
                'title' => __('Range From'),
                'required' => false,
                'note' => __('Enter the starting range for zip/post.'),
                'class' => 'hidden-range-field'
            ]
        );

        $fieldset->addField(
            'range_to',
            'text',
            [
                'name' => 'range_to',
                'label' => __('Range To'),
                'title' => __('Range To'),
                'required' => false,
                'note' => __('Enter the ending range for zip/post.'),
                'class' => 'hidden-range-field'
            ]
        );

        $fieldset->addField(
            'delivery_days',
            'multiselect',
            [
                'name' => 'delivery_days[]',
                'label' => __('Delivery Time'),
                'title' => __('Delivery Time'),
                'values' => $this->getDaysOfWeekOptions(),
                'required' => true
            ]
        );

        if ($zipId) {
            $zipData = $this->zipFactory->create()->load($zipId);
            $form->setValues($zipData->getData());
        }

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Provide options for the days of the week.
     *
     * @return array
     */
    private function getDaysOfWeekOptions()
    {
        return [
            ['label' => __('Monday'), 'value' => 'Monday'],
            ['label' => __('Tuesday'), 'value' => 'Tuesday'],
            ['label' => __('Wednesday'), 'value' => 'Wednesday'],
            ['label' => __('Thursday'), 'value' => 'Thursday'],
            ['label' => __('Friday'), 'value' => 'Friday'],
            ['label' => __('Saturday'), 'value' => 'Saturday'],
            ['label' => __('Sunday'), 'value' => 'Sunday'],
        ];
    }
}
