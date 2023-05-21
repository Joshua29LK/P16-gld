<?php
namespace Hoofdfabriek\BePostcodeNL\Block\Checkout;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class LayoutProcessor
 */
class LayoutProcessor extends AbstractBlock implements LayoutProcessorInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * LayoutProcessor constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * Add postcodenl fields to checkout
     *
     * @param array $result
     * @return array
     */
    public function process($result)
    {
        if ($this->scopeConfig->getValue('postcodenl/autocomplete/use_be_autocomplete', ScopeInterface::SCOPE_STORE) &&
            isset($result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['shipping-address-fieldset'])
        ) {
            $shippingFields = $result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children'];

            $shippingFields = array_merge($shippingFields, $this->getPostcodeFieldSet('shippingAddress', 'shipping'));

            $result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children'] = $shippingFields;
            $result = $this->getBillingFormFields($result);
        }

        return $result;
    }

    /**
     * Add postcodenl fields to billing
     *
     * @param array $result
     * @return array
     */
    public function getBillingFormFields($result)
    {
        if (isset(
            $result['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']['payments-list']
        )) {
            $paymentForms = $result['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']
            ['payments-list']['children'];
            foreach ($paymentForms as $paymentMethodForm => $paymentMethodValue) {
                $paymentMethodCode = str_replace('-form', '', $paymentMethodForm);
                if (!isset($result['components']['checkout']['children']['steps']['children']['billing-step']
                    ['children']['payment']['children']['payments-list']['children'][$paymentMethodCode . '-form'])) {
                    continue;
                }
                $billingFields = $result['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']
                ['payments-list']['children'][$paymentMethodCode . '-form']['children']['form-fields']['children'];
                $billingPostcodeFields = $this->getPostcodeFieldSet('billingAddress' . $paymentMethodCode, 'billing');
                $billingFields = array_merge($billingFields, $billingPostcodeFields);


                $result['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['payments-list']['children'][$paymentMethodCode . '-form']
                ['children']['form-fields']['children'] = $billingFields;
            }
        }

        //Add Mageplaza_Osc onestepcheckout support
        if (isset($result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['billingAddress']['children']
            ['billing-address-fieldset']['children'])) {
            $billingFields = $result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['billingAddress']['children']
            ['billing-address-fieldset']['children'];

            $billingPostcodeFields = $this->getPostcodeFieldSet('billingAddress', 'billing');
            //Replace template to hide street label if Belgium is selected by default
            $billingFields['street']['config']['template'] = 'Hoofdfabriek_PostcodeNL/ui/group/street';
            $billingFields = array_merge($billingFields, $billingPostcodeFields);

            $result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['billingAddress']['children']
            ['billing-address-fieldset']['children'] = $billingFields;
        }
        return $result;
    }

    /**
     * Postcode fields settings
     *
     * @param string $scope
     * @param string $addressType
     * @return array
     */
    public function getPostcodeFieldSet($scope, $addressType)
    {
        $postcodeFields =
            [
                'be_postcodenl_postcode' => [
                    'component' => 'Hoofdfabriek_BePostcodeNL/js/view/form/autocomplete',
                    'config' => [
                        "customScope" => $scope,
                        "loaderImageHref" => $this->getViewFileUrl('images/loader-1.gif'),
                    ],
                    'provider' => 'checkoutProvider',
                    'dataScope' => $scope,
                    'addressType' => $addressType,
                    'sortOrder' => '924',
                ],

            ];
        return $postcodeFields;
    }
}
