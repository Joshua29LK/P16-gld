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

class Apply extends Generic implements TabInterface
{
    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    private $orderConfig;
    /**
     * @var \Magento\Shipping\Model\Config\Source\Allmethods
     */
    private $shippingConfig;
    /**
     * @var \Amasty\Flags\Model\ResourceModel\Flag\CollectionFactory
     */
    private $flagCollectionFactory;
    /**
     * @var \Amasty\Flags\Model\ResourceModel\Column\CollectionFactory
     */
    private $columnCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Shipping\Model\Config\Source\Allmethods $shippingConfig,
        \Amasty\Flags\Model\ResourceModel\Flag\CollectionFactory $flagCollectionFactory,
        \Amasty\Flags\Model\ResourceModel\Column\CollectionFactory $columnCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->orderConfig = $orderConfig;
        $this->shippingConfig = $shippingConfig;
        $this->flagCollectionFactory = $flagCollectionFactory;
        $this->columnCollectionFactory = $columnCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Automatic Apply');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Automatic Apply');
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

        $fieldset = $form->addFieldset('apply_fieldset', [
            'legend' => __('Automatically Apply On Order Status Change And Selected Shipping Method')
        ]);

        $fieldset->addType(
            'partial_multiselect',
            \Amasty\Flags\Block\Adminhtml\Element\Multiselect::class
        );

        $columns = $this->columnCollectionFactory->create();

        $values = [['value' => null, 'label' => ' ']];
        /** @var \Amasty\Flags\Model\Column $column */
        foreach ($columns as $column) {
            $values[] = ['value' => $column->getId(), 'label' => $column->getName()];
        }

        $fieldset->addField('apply_column', 'select', [
            'name'      => 'apply_column',
            'label'     => __('Column name'),
            'title'     => __('Column name'),
            'values'    => $values,
            'note'      => __('Assign to column'),
        ]);

        $statuses = $this->orderConfig->getStatuses();

        $values   = [];
        foreach ($statuses as $code => $name) {
            $values[] = ['value' => $code, 'label' => $name];
        }

        $fieldset->addField('apply_status', 'multiselect', [
            'name'      => 'apply_status',
            'label'     => __('Order Status'),
            'title'     => __('Order Status'),
            'values'    => $values,
            'note'      => __('Set flag if order changes to one of selected statuses'),
        ]);

        // shipping methods
        $methods = $this->shippingConfig->toOptionArray(true);

        /** @var \Amasty\Flags\Model\ResourceModel\Flag\Collection $flags */
        $flags = $this->flagCollectionFactory->create();

        // disable shipping methods, selected in other flags
        $appliedMethods = [];
        /** @var Flag $flag */
        foreach ($flags as $i => $flag) {
            if ($flag->getId() != $model->getId() && $flag->getApplyShipping()) {
                // phpcs:ignore Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
                $appliedMethods = array_merge(
                    $appliedMethods,
                    explode(',', $flag->getApplyShipping())
                );
            }
        }

        foreach ($methods as &$carrier) {
            if (!is_array($carrier['value'])) {
                continue;
            }

            foreach ($carrier['value'] as &$method) {
                if (in_array($method['value'], $appliedMethods)) {
                    $method['disabled'] = true;
                }
            }
            unset($method);
        }

        $fieldset->addField('apply_shipping', 'partial_multiselect', [
            'name'      => 'apply_shipping',
            'label'     => __('Order Shipping Method'),
            'title'     => __('Order Shipping Method'),
            'values'    => $methods,
            'note'      => __(
                'Set flag if in the order used one of selected shipping methods.' .
                ' Each shipping method can be selected for only one flag.'
            ),
        ]);

        // payment methods
        $methods = $this->_scopeConfig->getValue('payment');

        // disable payment methods, selected in other flags
        foreach ($flags as $i => $flag) {
            if ($flag->getId() != $model->getId()) {
                $appliedMethods = explode(',', $flag->getApplyPayment());
                if ($appliedMethods) {
                    foreach ($appliedMethods as $j => $method) {
                        $methods[$method]['disabled'] = true;
                    }
                }
            }
        }

        $values = [];
        foreach ($methods as $code => $method) {
            $value = ['value' => $code];

            if (isset($method['title'])) {
                $value['label'] = $method['title'];
            } else {
                $value['label'] = $code;
            }

            if (isset($method['disabled'])) {
                $value['disabled'] = $method['disabled'];
            }

            $values[] = $value;
        }

        $fieldset->addField('apply_payment', 'partial_multiselect', [
            'name'      => 'apply_payment',
            'label'     => __('Order Payment Method'),
            'title'     => __('Order Payment Method'),
            'values'    => $values,
            'note'      => __(
                'Set flag if in the order used one of selected payment methods.' .
                ' Each payment method can be selected for only one flag.'
            ),
        ]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
