<?php
namespace Bss\CustomizeOsc\Override\Plugin\Block\Checkout\Checkout;

use Bss\OneStepCheckout\Helper\Config;
use Bss\OneStepCheckout\Helper\Data;

class LayoutProcessor extends \Bss\OneStepCheckout\Plugin\Block\Checkout\Checkout\LayoutProcessor
{
    /**
     * @var LayoutSettings
     */
    protected $layoutSettings;
    /**
     * @var \MSP\ReCaptcha\Model\Config
     */
    protected $config;

    /**
     * LayoutProcessor constructor.
     * @param Config $configHelper
     * @param Data $dataHelper
     * @param LayoutSettings $layoutSettings
     * @param \MSP\ReCaptcha\Model\Config $config
     */

    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ) {
        if (!$this->configHelper->isEnabled()) {
            return $jsLayout;
        }
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['afterMethods']['children']['billing-address-form'])) {
            $component = $jsLayout['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']['afterMethods']['children']
            ['billing-address-form'];
            unset(
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['afterMethods']['children']['billing-address-form']
            );
            
            $component['component'] = 'Bss_OneStepCheckout/js/view/billing-address';
            // Customize show billing-addres on shipping address
            $component['sortOrder'] = 1000;
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['billing-address-form'] = $component;
        }



        $jsLayout = $this->orderDeliveryDate($jsLayout);

        if (!$this->configHelper->isDisplayField('enable_order_comment')) {
            unset(
                $jsLayout['components']['checkout']['children']['sidebar']['children']
                ['bss_osc_order_comment']
            );
        }

        $jsLayout = $this->newsletter($jsLayout);

        if (!$this->configHelper->isGiftMessageField('enable_gift_message') ||
            !$this->configHelper->isMessagesAllowed()) {
            unset(
                $jsLayout['components']['checkout']['children']['sidebar']['children']
                ['giftmessage']
            );
        }

        if ($this->configHelper->getGiftWrapFee() === false) {
            unset(
                $jsLayout['components']['checkout']['children']['sidebar']['children']['gift_wrap']
            );
        }

        $jsLayout = $this->discountCode($jsLayout);

        $jsLayout = $this->removeComponent($jsLayout);
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment'])) {
            $payment = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment'];
            unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']);
            $payment['sortOrder'] = 500;
            $payment['displayArea'] = 'summary';
            $payment['children']['payments-list']['config']['deps']['0'] = 'checkout.sidebar.payment.renders';
            $payment['children']['payments-list']['config']['deps']['1'] = 'checkout.sidebar.payment.additional-payment-validators';
            $jsLayout['components']['checkout']['children']['sidebar']['children']['payment'] = $payment;

        }
        return $jsLayout;
    }
}
