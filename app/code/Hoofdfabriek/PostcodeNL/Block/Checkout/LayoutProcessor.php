<?php
namespace Hoofdfabriek\PostcodeNL\Block\Checkout;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Store\Model\ScopeInterface;
use Hoofdfabriek\PostcodeNL\Model\Config;

/**
 * Class LayoutProcessor
 */
class LayoutProcessor extends AbstractBlock implements LayoutProcessorInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * LayoutProcessor constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->config = $config;
    }

    /**
     * Add postcodenl fields to checkout
     *
     * @param array $result
     * @return array
     */
    public function process($result)
    {
        if ($this->config->isPostcodeNLEnabled() &&
            isset($result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['shipping-address-fieldset'])
        ) {
            $shippingFields = $result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children'];

            //Replace template to hide street label if Belgium is selected by default
            $shippingFields['street']['config']['template'] = 'Hoofdfabriek_PostcodeNL/ui/group/street';
            $shippingFields = array_merge($shippingFields, $this->getPostcodeFieldSet('shippingAddress', 'shipping'));

            $shippingFields = array_merge($shippingFields, $this->getPostcodeFieldSet('shippingAddress', 'shipping'));

            $shippingFields = $this->changeAddressFieldPosition($shippingFields);

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
                //Replace template to hide street label if Belgium is selected by default
                $billingFields['street']['config']['template'] = 'Hoofdfabriek_PostcodeNL/ui/group/street';
                $billingFields = array_merge($billingFields, $billingPostcodeFields);

                $billingFields = $this->changeAddressFieldPosition($billingFields);

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

            $billingFields = $this->changeAddressFieldPosition($billingFields);

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
                'postcodenl_postcode' => [
                    'component' => 'Hoofdfabriek_PostcodeNL/js/view/form/postcode',
                    'config' => [
                        "customScope" => $scope,
                        "loaderImageHref" => $this->getViewFileUrl('images/loader-1.gif'),
                    ],
                    'provider' => 'checkoutProvider',
                    'dataScope' => $scope,
                    'messageText' => 'Enter your zip code and house number and your address will be completed automatically.',
                    'addressType' => $addressType,
                    'sortOrder' => '924',
                ],

            ];
        return $postcodeFields;
    }

    /**
     * Change order of input fields
     *
     * @param array $addressFields
     * @return array
     */
    public function changeAddressFieldPosition($addressFields)
    {
        if (isset($addressFields['country_id'])) {
            $addressFields['country_id']['sortOrder'] = '900';
        }
        if (isset($addressFields['street'])) {
            $addressFields['street']['sortOrder'] = '940';
        }
        if (isset($addressFields['city'])) {
            $addressFields['city']['sortOrder'] = '950';
        }

        if (isset($addressFields['postcode'])) {
            $addressFields['postcode']['sortOrder'] = '930';
        }

        if (isset($addressFields['region'])) {
            $addressFields['region']['sortOrder'] = '933';
        }
        if (isset($addressFields['region_id'])) {
            $addressFields['region_id']['sortOrder'] = '935';
        }

        if (isset($addressFields['telephone'])) {
            $addressFields['telephone']['sortOrder'] = '1010';
        }

        return $addressFields;
    }
}
