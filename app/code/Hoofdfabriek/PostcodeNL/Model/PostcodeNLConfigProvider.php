<?php
namespace Hoofdfabriek\PostcodeNL\Model;

use Hoofdfabriek\PostcodeNL\Model\Config;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\UrlInterface;

/**
 * Class PostcodeNLConfigProvider
 */
class PostcodeNLConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * PostcodeNLConfigProvider constructor.
     * @param Config $config
     */
    public function __construct(
        Config $config,
        UrlInterface $urlBuilder
    ) {
        $this->config = $config;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $config = [
            'hoofdfabriek_postcode' => [
                'settings' => [
                    "useStreet2AsHouseNumber" => $this->config->isSecondLineForHouseNumber(),
                    "useStreet3AsHouseNumberAddition" => $this->config->isThirdLineForHouseAddition(),
                    "url" => $this->urlBuilder->getUrl('postcodenl/lookup/'),
                    "translations" => [
                        "defaultError" => htmlspecialchars(__('Unknown postcode + housenumber combination.'))
                    ]
                ]
            ]
        ];
        return $config;
    }
}
