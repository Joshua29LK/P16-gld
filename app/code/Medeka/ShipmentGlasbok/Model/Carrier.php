<?php

namespace Medeka\ShipmentGlasbok\Model;

use Magento\Quote\Model\Quote\Address\RateResult\Error;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Simplexml\Element;

class Carrier extends AbstractCarrier implements CarrierInterface
{
    const CODE = 'gbcustomshipping';
    protected $_code = self::CODE;

    protected $scopeConfig;
    protected $rateErrorFactory;
    protected $logger;
    protected $rateFactory;
    protected $rateMethodFactory;
    protected $trackFactory;
    protected $trackErrorFactory;
    protected $trackStatusFactory;
    protected $regionFactory;
    protected $countryFactory;
    protected $currencyFactory;
    protected $directoryData;
    protected $stockRegistry;
    protected $localeFormat;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->rateErrorFactory = $rateErrorFactory;
        $this->logger = $logger;
        $this->rateFactory = $rateFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->trackFactory = $trackFactory;
        $this->trackErrorFactory = $trackErrorFactory;
        $this->trackStatusFactory = $trackStatusFactory;
        $this->regionFactory = $regionFactory;
        $this->countryFactory = $countryFactory;
        $this->currencyFactory = $currencyFactory;
        $this->directoryData = $directoryData;
        $this->stockRegistry = $stockRegistry;
        $this->localeFormat = $localeFormat;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    public function getAllowedMethods()
    {
    }

    /**
     * Collect and get rates
     *
     * @param RateRequest $request
     * @return Result|bool|null
     */
    public function collectRates(RateRequest $request)
    {
        $result = $this->rateFactory->create();
        $method = $this->rateMethodFactory->create();
        $method->setCarrier($this->_code);
        $method->setCarrierTitle('Glas leveren op glasbok ');
        /* Set method name */
        $method->setMethod($this->_code);
        $method->setMethodTitle('Glas leveren op glasbok ');
        $method->setCost(0);
        /* Set shipping charge */
        $method->setPrice(15);
        $result->append($method);
        return $result;
    }

    /**
     * Processing additional validation to check if the carrier is applicable.
     *
     * @param \Magento\Framework\DataObject $request
     * @return $this|bool|\Magento\Framework\DataObject
     */
    public function proccessAdditionalValidation(\Magento\Framework\DataObject $request)
    {
        return true;
    }
}
