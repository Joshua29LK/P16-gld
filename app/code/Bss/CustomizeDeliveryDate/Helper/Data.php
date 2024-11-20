<?php

namespace Bss\CustomizeDeliveryDate\Helper;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Bss\CustomizeDeliveryDate\Model\ZipDeliveryRepository;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Serialize
     */
    protected $serialize;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $file;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var ConvertDate
     */
    protected $helperDate;

    protected $zipDeliveryRepository;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param TimezoneInterface $localeDate
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Serialize\Serializer\Serialize $serialize
     * @param \Magento\Framework\Filesystem\Driver\File $file
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Bss\OrderDeliveryDate\Helper\ConvertDate $helperDate
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Serialize\Serializer\Serialize $serialize,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Bss\OrderDeliveryDate\Helper\ConvertDate $helperDate,
        \Bss\CustomizeDeliveryDate\Model\ZipDeliveryRepository $zipDeliveryRepository
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->localeDate = $localeDate;
        $this->date = $date;
        $this->storeManager = $storeManager;
        $this->serialize = $serialize;
        $this->file = $file;
        $this->productMetadata = $productMetadata;
        $this->helperDate = $helperDate;
        $this->zipDeliveryRepository = $zipDeliveryRepository;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Filesystem\Driver\File
     */
    public function returnDriverFile()
    {
        return $this->file;
    }

    /**
     * @return \Magento\Framework\Serialize\Serializer\Serialize
     */
    public function returnClassSerialize()
    {
        return $this->serialize;
    }
    /**
     * @return bool
     */
    public function isEnabled()
    {
        $active =  $this->scopeConfig->getValue(
            'orderdeliverydate/general/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($active != 1) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isShowShippingComment()
    {
        $active =  $this->scopeConfig->getValue(
            'orderdeliverydate/general/shipping_comment',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($active != 1) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isAsProcessingDays()
    {
        $active =  $this->scopeConfig->getValue(
            'orderdeliverydate/general/as_processing_days',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($active != 1) {
            return false;
        }

        return true;
    }

    /**
     * @return integer
     */
    public function getDisplayAt()
    {
        $active =  $this->scopeConfig->getValue(
            'orderdeliverydate/general/on_which_page',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $active;
    }

    /**
     * @return int
     */
    public function getProcessingTime()
    {
        $process_time =  $this->scopeConfig->getValue(
            'orderdeliverydate/general/process_time',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!$process_time) {
            return 0;
        }
        return $process_time;
    }

    /**
     * @return bool|false|int
     */
    public function getCutOffTime()
    {
        $cut_off_time =  $this->scopeConfig->getValue(
            'orderdeliverydate/general/cut_off_time',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!$cut_off_time || $cut_off_time == '00,00,00') {
            return false;
        }

        $cutOffDate = $this->localeDate->date()->format('Y-m-d') . ' ' . str_replace(',', ':', $cut_off_time);
        $cut_off_time_convert = strtotime($cutOffDate);

        return $cut_off_time_convert;
    }

    /**
     * @return string
     */
    public function getBlockHoliday()
    {
        $block_out_holidays =  $this->scopeConfig->getValue(
            'orderdeliverydate/general/block_out_holidays',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $response = [];
        if ($block_out_holidays) {
            $block_out_holidays_arr = $this->serialize->unserialize($block_out_holidays);
            if ($block_out_holidays_arr) {
                foreach ($block_out_holidays_arr as $holidays) {
                    $newDate = date("Y-m-d", strtotime($holidays['date']));
                    $response[] = $newDate;
                }
            }
        }
        return $this->serialize->serialize($response);
    }

    /**
     * @return array
     */
    public function getTimeSlot()
    {
        $time_slots = $this->scopeConfig->getValue(
            'orderdeliverydate/general/time_slots',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($time_slots) {
            $time_slot_arr = $this->serialize->unserialize($time_slots);
            return $this->getClearTimeSlot($time_slot_arr);
        }
        return [];
    }

    /**
     * @param $timeSlotArr
     * @return array
     */
    protected function getClearTimeSlot($timeSlotArr)
    {
        $result = [];
        $processingDay = $this->getProcessingTime();
        $now = $this->getStoreTimestamp();
        if (is_array($timeSlotArr) && !empty($timeSlotArr)) {
            foreach ($timeSlotArr as $timeSlot) {
                $timeFrom = $this->convertAMPM($timeSlot['from']);
                $timeTo = $this->convertAMPM($timeSlot['to']);
                if (!$timeFrom || !$timeTo) {
                    continue;
                }
                $disabled = 0;
                if ($this->timeLineCondition($timeFrom, $timeTo, $now) && $processingDay == 0) {
                    $disabled = 1;
                }
                $a = $timeSlot['note'] . ' ' . $timeSlot['from'] . ' - ' . $timeSlot['to'];
                $b = ['value' => $a, 'label' => $a, 'disabled' => $disabled];
                array_push($result, $b);
            }
        }
        return $result;
    }

    /**
     * @param $timeFrom
     * @param $timeTo
     * @param $now
     * @return bool
     */
    public function timeLineCondition($timeFrom, $timeTo, $now)
    {
        // $timeFrom < $now < $timeTo
        // $timeFrom, $timeTo < $now
        // are disabled
        return (($timeFrom < $now && $timeTo > $now) || ($timeTo < $now && $timeFrom < $now));
    }

    /**
     * @param string $strTime
     * @return false|int
     */
    protected function convertAMPM($strTime)
    {
        $exp = "/^[0-9][0-9]:[0-9][0-9]\s[AM|PM]/i"; // Regex check AM PM time
        if (preg_match($exp, $strTime)) {
            return $this->date->gmtTimestamp(date('Y-m-d') . ' ' . $strTime);
        }
        return false;
    }

    /**
     * @return bool|mixed
     */
    public function getDayOff()
    {
        $day_off =  $this->scopeConfig->getValue(
            'orderdeliverydate/general/deliverydate_day_off',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($day_off === null) {
            return false;
        }
        return $day_off;
    }

    /**
     * @return bool|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getIcon()
    {
        $icon =  $this->scopeConfig->getValue(
            'orderdeliverydate/general/icon_calendar',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!isset($icon)) {
            return false;
        }
        return $this->getMediaUrl() . 'bss/deliverydate/' . $icon;
    }

    /**
     * @return string
     */
    public function getDateFormat()
    {
        $dateFormat = $this->scopeConfig->getValue(
            'orderdeliverydate/general/date_fields',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($dateFormat) {
            switch ($dateFormat) {
                case 1:
                    return 'yy/mm/dd';
                case 2:
                    return 'dd-mm-yy';
                case 3:
                    return 'yy-mm-dd';
                default:
                    return 'yy/mm/dd';
            }
        }
        return 'yy/mm/dd';
    }

    /**
     * @param null $date
     * @return string
     */
    public function formatDate($date = null)
    {
        $dateFormat = $this->getDateFormat();
        if ($dateFormat) {
            switch ($dateFormat) {
                case 'mm/dd/yy':
                    $dateFormat = 'm/d/Y';
                    break;
                case 'dd-mm-yy':
                    $dateFormat = 'd-m-Y';
                    break;
                case 'yy-mm-dd':
                    $dateFormat = 'Y-m-d';
                    break;
                default:
                    $dateFormat = 'm/d/y';
                    break;
            }
        }
        if ($date) {
            return $this->helperDate->scopeDate(null, $date, false)->format($dateFormat);
        } else {
            return $dateFormat;
        }
    }

    /**
     * @param null $store
     * @return int
     */
    public function getStoreTimestamp($store = null)
    {
        return $this->localeDate->scopeTimeStamp($store);
    }

    /**
     * @return int
     */
    public function getTimezoneOffsetSeconds()
    {
        return $this->date->getGmtOffset();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @param string $field
     * @return bool
     */
    public function isFieldRequired($field)
    {
        $enable =  $this->scopeConfig->getValue(
            'orderdeliverydate/general/'. $field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($enable != 1) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPaymentSaveAction()
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        $action = $baseUrl."orderdeliverydate/payment/saveDelivery";
        return $action;
    }

    /**
     * @return string
     */
    public function getDateToday()
    {
        $dateFormat = $this->getDateFormat();
        if ($dateFormat) {
            switch ($dateFormat) {
                case 'mm/dd/yy':
                    $dateFormat = 'm/d/Y';
                    break;
                case 'dd-mm-yy':
                    $dateFormat = 'd-m-Y';
                    break;
                case 'yy-mm-dd':
                    $dateFormat = 'Y-m-d';
                    break;
                default:
                    $dateFormat = 'm/d/y';
                    break;
            }
        }
        return $this->date->date($dateFormat);
    }

    /**
     * @param null $scopeType
     * @param null $scopeCode
     * @return string
     */
    public function getStoreTimezone($scopeType = null, $scopeCode = null)
    {
        return $this->localeDate->getConfigTimezone($scopeType, $scopeCode);
    }

    /**
     * @return false|string
     */
    public function getDayOfWeekName()
    {
        return $this->localeDate->date()->format('l');
    }

    /**
     * @return bool
     */
    public function isLowerThan241Version()
    {
        $version = $this->productMetadata->getVersion();
        $checkVersion = version_compare($version, '2.4.0', '<=');
        $checkVersion1 = version_compare($version, '2.3.6', '!=');
        return $checkVersion && $checkVersion1;
    }

    public function getAllZip()
    {
        return $this->zipDeliveryRepository->getAll();
    }

    public function getLevertijdIndicatieConfig()
    {
        $configPath = 'orderdeliverydate/general/levertijd_indicatie';
        $configValue = $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return json_decode($configValue, true);
    }

    public function getCountryNotAllow()
    {
        $configPath = 'orderdeliverydate/general/orderdeliverydate_countries';
        $configValue = $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!$configValue) {
            return [];
        }

        return explode(',', $configValue);
    }
}
