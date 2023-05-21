<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Custom Order Number for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Number\Model;

use Amasty\Base\Model\ConfigProviderAbstract;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class ConfigProvider extends ConfigProviderAbstract
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    public const XPATH_PREFIX = 'amnumber/';
    public const XPATH_ENABLED = 'general/enabled';
    public const XPATH_OFFSET = 'general/offset';
    public const XPATH_SEPARATE_CONNECTION = 'general/separate_connection';
    /**#@-*/

    public const PART_NUMBER_FORMAT = '/format';
    public const PART_NUMBER_COUNTER = '/counter';
    public const PART_NUMBER_SAME = '/same';
    public const PART_NUMBER_PREFIX = '/prefix';
    public const PART_NUMBER_PREFIX_REPLACE = '/replace';
    public const PART_COUNTER_FROM = '/start';
    public const PART_INCREMENT_STEP = '/increment';
    public const PART_COUNTER_PAD = '/pad';
    public const PART_COUNTER_RESET_DATE = '/reset';
    public const PART_SEPARATE_WEBSITE = '/per_website';
    public const PART_SEPARATE_STORE = '/per_store';

    public const DEFAULT_COUNTER_STEP = 1;
    public const ORDER_TYPE = 'order';
    public const INVOICE_TYPE = 'invoice';
    public const SHIPMENT_TYPE = 'shipment';
    public const CREDITMEMO_TYPE = 'creditmemo';

    public const AVAILABLE_ENTITY_TYPES = [
        self::ORDER_TYPE,
        self::INVOICE_TYPE,
        self::SHIPMENT_TYPE,
        self::CREDITMEMO_TYPE
    ];

    /**
     * @var string
     */
    protected $pathPrefix = self::XPATH_PREFIX;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var App\State
     */
    private $state;

    /**
     * @var int
     */
    private $storeId;

    public function __construct(
        App\State $state,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($scopeConfig);
        $this->storeManager = $storeManager;
        $this->state = $state;
    }

    /**
     * @return bool
     */
    public function isEnabled(?int $storeId = null): bool
    {
        return $this->isSetFlag(self::XPATH_ENABLED, $storeId);
    }

    /**
     * @return mixed
     */
    public function getTimezoneOffset()
    {
        return $this->getValue(self::XPATH_OFFSET);
    }

    /**
     * @return bool
     */
    public function isUseSeparateConnection(): bool
    {
        return $this->isSetFlag(self::XPATH_SEPARATE_CONNECTION);
    }

    /**
     * @param string $type
     * @return string
     */
    public function getNumberFormat(string $type): string
    {
        if ($type !== self::ORDER_TYPE && $this->isFormatSameAsOrder($type)) {
            return $this->getNumberFormat(self::ORDER_TYPE);
        }

        return (string)$this->getScopedValue($type . self::PART_NUMBER_FORMAT);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function isFormatSameAsOrder($type): bool
    {
        return !!$this->getScopedValue($type . self::PART_NUMBER_SAME);
    }

    /**
     * @param string $type
     * @return string
     */
    public function getNumberPrefix($type)
    {
        return (string)$this->getScopedValue($type . self::PART_NUMBER_PREFIX);
    }

    /**
     * @param string $type
     * @return string
     */
    public function getNumberReplacePrefix($type)
    {
        return (string)$this->getScopedValue($type . self::PART_NUMBER_PREFIX_REPLACE);
    }

    /**
     * @param string $type
     * @return int
     */
    public function getStartCounterFrom($type): int
    {
        $start = (int)$this->getScopedValue($type . self::PART_COUNTER_FROM);

        if ($start <= 0) {
            $start = self::DEFAULT_COUNTER_STEP;
        }

        return $start;
    }

    /**
     * @param string $type
     * @return int
     */
    public function getCounterStep($type): int
    {
        $step = (int)$this->getScopedValue($type . self::PART_INCREMENT_STEP);

        if ($step <= 0) {
            $step = self::DEFAULT_COUNTER_STEP;
        }

        return $step;
    }

    /**
     * @param string $type
     * @return int
     */
    public function getCounterPadding($type): int
    {
        return (int)$this->getScopedValue($type . self::PART_COUNTER_PAD);
    }

    /**
     * @param string $type
     * @return string
     */
    public function getCounterResetOnDateChange($type): string
    {
        return (string)$this->getScopedValue($type . self::PART_COUNTER_RESET_DATE);
    }

    /**
     * @param $type
     * @param string $scope
     * @param $scopeId
     * @return bool
     */
    public function isSeparateCounter($type, $scope = ScopeInterface::SCOPE_STORE, $scopeId = null): bool
    {
        switch ($scope) {
            case ScopeInterface::SCOPE_WEBSITE:
                return !!$this->getValue(
                    $type . self::PART_SEPARATE_WEBSITE,
                    $scopeId,
                    ScopeInterface::SCOPE_WEBSITE
                );
            case ScopeInterface::SCOPE_STORE:
                return !!$this->getValue(
                    $type . self::PART_SEPARATE_STORE,
                    $scopeId,
                    ScopeInterface::SCOPE_STORE
                );
            default:
                return false;
        }
    }

    /**
     * @param $storeId
     */
    public function setStoreId($storeId)
    {
        $this->storeId = (int)$storeId;
    }

    /**
     * Get scope data on admin within defined storeID via setStoreId() method.
     * Counter number config must have correct scope during Order placement or Invoice/Shipping/Memo creating on admin
     * because scope config could not be automatically resolved on admin area.
     *
     * @param $path
     * @return mixed
     */
    private function getScopedValue($path)
    {
        try {
            if (!$this->isAdminArea()) {
                return $this->getValue($path);
            }

            $storeValue = $this->getValue(
                $path,
                $this->storeManager->getStore($this->storeId)->getId(),
                ScopeInterface::SCOPE_STORE
            );
            $websiteValue = $this->getValue(
                $path,
                $this->storeManager->getStore($this->storeId)->getWebsiteId(),
                ScopeInterface::SCOPE_WEBSITE
            );
        } catch (\Throwable $e) {
            null;
        }

        return $storeValue ?? $websiteValue ?? $this->getValue($path);
    }

    /**
     * @return bool
     */
    private function isAdminArea()
    {
        try {
            return $this->state->getAreaCode() === App\Area::AREA_ADMINHTML;
        } catch (\Throwable $e) {
            null;
        }

        return false;
    }
}
