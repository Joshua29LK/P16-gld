<?php
namespace Hoofdfabriek\BePostcodeNL\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class PostcodeNLConfigProvider
 */
class BePostcodeNLConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * BePostcodeNLConfigProvider constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $config = [
            'be_autocomplete' => [
                'settings' => [
                    "url" => $this->urlBuilder->getUrl('bepostcodenl/autocomplete/'),
                ]
            ]
        ];
        return $config;
    }
}
