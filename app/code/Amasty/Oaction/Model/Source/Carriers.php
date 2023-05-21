<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Model\Source;

use Magento\Shipping\Model\Config;

class Carriers implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var Config
     */
    private $shippingConfig;

    public function __construct(
        Config $shippingConfig
    ) {
        $this->shippingConfig = $shippingConfig;
    }

    public function toOptionArray()
    {
        $options = [
            [
                'value' => '',
                'label' => __('Do not need a tracking info')
            ],
            [
                'value' => 'custom',
                'label' => __('Custom')
            ]
        ];

        foreach ($this->shippingConfig->getAllCarriers() as $code => $carrier) {
            if ($carrier->isTrackingAvailable()) {
                $options[] = [
                    'value' => $code,
                    'label' => $carrier->getConfigData('title'),
                ];
            }
        }

        return $options;
    }
}
