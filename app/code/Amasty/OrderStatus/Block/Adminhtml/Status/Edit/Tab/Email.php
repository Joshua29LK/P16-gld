<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Status for Magento 2
 */

namespace Amasty\OrderStatus\Block\Adminhtml\Status\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Email\Model\ResourceModel\Template\CollectionFactory;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

class Email extends Generic implements TabInterface
{
    public const NOTIFY_DISABLED = 0;
    public const NOTIFY_ENABLED = 1;
    public const NOTIFY_OPTIONAL = 2;

    /**
     * @var CollectionFactory
     */
    private $templateCollectionFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        CollectionFactory $templateCollectionFactory,
        array $data = []
    ) {
        $this->templateCollectionFactory = $templateCollectionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function getTabLabel()
    {
        return __('E-mail Notifications');
    }

    public function getTabTitle()
    {
        return __('E-mail Notifications');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_amasty_order_status');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('amostatus_');
        $fieldsetNotifications = $form->addFieldset(
            'notifications_fieldset',
            ['legend' => __('Enable Notifications')]
        );

        $fieldsetNotifications->addField(
            'notify_by_email',
            'select',
            [
                'name' => 'notify_by_email',
                'label' => __('Always Notify Customer By E-mail'),
                'title' => __('Always Notify Customer By E-mail'),
                'options' => [
                    self::NOTIFY_DISABLED => __('No'),
                    self::NOTIFY_ENABLED => __('Yes'),
                    self::NOTIFY_OPTIONAL => __('Optional')
                ],
                'note' => __(
                    'If set to `Yes`, customer always gets e-mail notification '
                    . 'when order status is set to the current one'
                )
            ]
        );

        $fieldsetStoreviewEmailTemplate = $form->addFieldset(
            'store_view_email_template_fieldset',
            ['legend' => __('Store View E-mail Template')]
        );

        $storeViews = $this->_storeManager->getStores();

        /** @var \Magento\Email\Model\ResourceModel\Template\Collection $optionsModel */
        $optionsModel = $this->templateCollectionFactory->create();
        $options = $this->_toOptions($optionsModel);
        $options[0] = __('Order Status Change (Default Template From Locale)');

        foreach ($storeViews as $storeView) {
            $fieldsetStoreviewEmailTemplate->addField(
                'store_template[' . $storeView->getStoreId() . ']',
                'select',
                [
                    'name' => 'store_template[' . $storeView->getStoreId() . ']',
                    'label' => '"' . $storeView->getName() . '" ' . __('Store Template'),
                    'title' => __('Store Template'),
                    'options' => $options
                ]
            );
        }

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    protected function _toOptions($collection)
    {
        $options = [];

        foreach ($collection as $item) {
            $options[$item->getTemplateId()] = $item->getTemplateCode();
        }

        return $options;
    }
}
