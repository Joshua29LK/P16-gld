<?php
namespace Hoofdfabriek\PostcodeNL\Block\Customer;

use Magento\Customer\Block\Address\Edit;
use Hoofdfabriek\PostcodeNL\Model\Config;

/**
 * Class Address
 */
class Address extends Edit
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param array $data
     * @param AttributeChecker $attributeChecker
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        Config $config
    ) {
        parent::__construct(
            $context,
            $directoryHelper,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $customerSession,
            $addressRepository,
            $addressDataFactory,
            $currentCustomer,
            $dataObjectHelper
        );

        $this->config = $config;
    }

    /**
     * @return bool
     */
    public function isSecondLineForHouseNumber()
    {
        return $this->config->isSecondLineForHouseNumber();
    }

    /**
     * @return bool
     */
    public function isThirdLineForHouseAddition()
    {
        return $this->config->isThirdLineForHouseAddition();
    }

    /**
     * @return array|bool
     */
    public function isEnabled()
    {
        return $this->config->isPostcodeNLEnabled() === true;
    }

    /**
     * Parse street
     *
     * @return null|array
     */
    protected function parseStreet()
    {
        $street = $this->getAddress()->getStreet() ?: [];
        preg_match(
            '/^(?P<address>\d*\D+[^A-Z1-9]) (?P<number>[^a-z]?\D*\d+.*)$/',
            implode(' ', $street),
            $match);
        return $match;
    }

    /**
     * Get house number from the street
     *
     * @return string
     */
    public function getHouseNumber()
    {
        $houseNumber = '';
        if ($match = $this->parseStreet()) {
            $houseNumber = $match['number'];
        }
        return $houseNumber;
    }

    /**
     * Get street name
     *
     * @return string
     */
    public function getSteet()
    {
        $street = '';
        if ($match = $this->parseStreet()) {
            $street = $match['address'];
        } else {
            $street = implode(' ', $this->getAddress()->getStreet() ?: []);
        }
        return $street;
    }
}
