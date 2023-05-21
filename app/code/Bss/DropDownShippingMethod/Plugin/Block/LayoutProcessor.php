<?php

namespace Bss\DropDownShippingMethod\Plugin\Block;

use Magento\Customer\Model\AttributeMetadataDataProvider;
use Magento\Ui\Component\Form\AttributeMapper;
use Magento\Checkout\Block\Checkout\AttributeMerger;
use Magento\Checkout\Model\Session as CheckoutSession;

class LayoutProcessor
{
    /**
     * @var AttributeMetadataDataProvider
     */
    public $attributeMetadataDataProvider;

    /**
     * @var AttributeMapper
     */
    public $attributeMapper;

    /**
     * @var AttributeMerger
     */
    public $merger;

    /**
     * @var CheckoutSession
     */
    public $checkoutSession;

    /**
     * @var null
     */
    public $quote = null;

    /**
     * LayoutProcessor constructor.
     * @param AttributeMetadataDataProvider $attributeMetadataDataProvider
     * @param AttributeMapper $attributeMapper
     * @param AttributeMerger $merger
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        AttributeMetadataDataProvider $attributeMetadataDataProvider,
        AttributeMapper $attributeMapper,
        AttributeMerger $merger,
        CheckoutSession $checkoutSession
    ) {
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->attributeMapper = $attributeMapper;
        $this->merger = $merger;
        $this->checkoutSession = $checkoutSession;
    }


    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    )
    {
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']
        ['children']['customer-email']
        ['children']['before-login-form']
        ['children']['switch_field'] = $this->getSwitchField();

        return $jsLayout;
    }


    protected function getSwitchField()
    {
        $customAttributeCode = 'switch_field';
        return [
            'component' => 'Bss_DropDownShippingMethod/js/view/form/element/radio',
            'config' => [
                'customScope' => 'shippingAddress.drop_downshipping',
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => 'Bss_DropDownShippingMethod/form/element/radio'
            ],
            'dataScope' => 'shippingAddress.drop_downshipping' . '.' . $customAttributeCode,
            'provider' => 'checkoutProvider',
            'sortOrder' => 0,
            'validation' => [
                'required-entry' => false
            ],
            'options' => [],
            'filterBy' => null,
            'customEntry' => null,
            'visible' => true,
        ];
    }
}
