<?php

namespace Bss\CustomizeDeliveryDate\Model;

use Bss\CustomizeDeliveryDate\Helper\Data;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Config\Model\Config\Source\Locale\Weekdays;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;

class CompositeConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{

    const TIME_OF_DAY_IN_SECONDS = 86400;

    /**
     * @var \Bss\OrderDeliveryDate\Helper\Data
     */
    protected $bssHelper;

    /**
     * @var \Magento\Config\Model\Config\Source\Locale\Weekdays
     */
    protected $weekdays;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var ProductResource
     */
    protected $productResource;

    /**
     * CompositeConfigProvider constructor.
     * @param Data $bssHelper
     * @param Weekdays $weekdays
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        \Bss\CustomizeDeliveryDate\Helper\Data $bssHelper,
        \Magento\Config\Model\Config\Source\Locale\Weekdays $weekdays,
        CheckoutSession $checkoutSession,
        ProductResource $productResource
    ) {
        $this->bssHelper = $bssHelper;
        $this->weekdays = $weekdays;
        $this->checkoutSession = $checkoutSession;
        $this->productResource = $productResource;
    }

    /**
     * Add ODD variable to Checkout Page
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfig()
    {
        $output = [];
        if ($this->bssHelper->isEnabled()) {
            $output['bss_delivery_enable'] = (boolean) $this->bssHelper->isEnabled();
            if ($this->bssHelper->getTimeSlot()) {
                $output['bss_delivery_timeslot'] = $this->bssHelper->getTimeSlot();
                $output['bss_delivery_has_timeslot'] = false;
            }
            $day_off = $this->bssHelper->getDayOff();
            $block_out_holidays = $this->bssHelper
                ->returnClassSerialize()
                ->unserialize($this->bssHelper->getBlockHoliday());
            $current_time = (int) $this->bssHelper->getStoreTimestamp();
            $cut_off_time_convert = $this->bssHelper->getCutOffTime();

            $dayMapping = [
                "Monday" => 1,
                "Tuesday" => 2,
                "Wednesday" => 3,
                "Thursday" => 4,
                "Friday" => 5,
                "Saturday" => 6,
                "Sunday" => 0
            ];

            $listZipDelivery = $this->bssHelper->getAllZip();
            $processedZipDelivery = array_map(function($item) use ($dayMapping) {
                $deliveryDays = explode(',', $item['delivery_days']);

                $missingDays = array_filter(array_keys($dayMapping), function ($day) use ($deliveryDays) {
                    return !in_array($day, $deliveryDays);
                });

                $missingDaysNumbers = array_map(function ($day) use ($dayMapping) {
                    return $dayMapping[$day];
                }, $missingDays);

                return [
                    'zip_code' => $item['zip_code'],
                    'delivery_days' => implode(',', $missingDaysNumbers)
                ];
            }, $listZipDelivery);

            $quote = $this->checkoutSession->getQuote();
            $listLevertijdIndicatie = $this->bssHelper->getLevertijdIndicatieConfig();
            $maxDay = 0;

            foreach ($quote->getAllItems() as $item) {
                $deliveryValue = $this->productResource->getAttributeRawValue(
                    $item->getProductId(),
                    ['levertijd_indicatie'],
                    $item->getStoreId()
                );
                $deliveryText = $this->productResource->getAttribute('levertijd_indicatie')->getSource()->getOptionText($deliveryValue);

                foreach ($listLevertijdIndicatie as $key => $configItem) {
                    if (isset($configItem['levertijd_indicatie']) && $configItem['levertijd_indicatie'] === $deliveryText) {
                        $day =  $configItem['day'] ?? null;
                        if ($day !== null && ($maxDay === null || $day > $maxDay)) {
                            $maxDay = $day;
                        }
                    }
                }
            }

            $process_time = $maxDay;
            if ($cut_off_time_convert &&
                $current_time > $cut_off_time_convert &&
                !$this->isProcessingDayDisabled()) {
                $process_time++;
            }
            
            $block_out_holidays = !empty($block_out_holidays) ? json_encode($block_out_holidays) : '';
            $output['bss_shipping_comment'] = false;
            $output['bss_delivery_process_time'] = $process_time;
            $output['bss_delivery_block_out_holidays'] = $block_out_holidays;
            $output['bss_delivery_day_off'] = $day_off;
            $output['bss_delivery_date_fomat'] = $this->bssHelper->getDateFormat();
            $output['bss_delivery_current_time'] = $current_time;
            $output['bss_delivery_time_zone'] = $this->bssHelper->getTimezoneOffsetSeconds();
            $output['as_processing_days'] = $this->bssHelper->isAsProcessingDays();
            $output['store_time_zone'] = $this->bssHelper->getStoreTimezone();
            if ($this->bssHelper->getIcon()) {
                $output['bss_delivery_icon'] = $this->bssHelper->getIcon();
            }
            $output['date_field_required'] = $this->bssHelper->isFieldRequired('required_date');
            $output['times_field_required'] = $this->bssHelper->isFieldRequired('required_timeslot');
            $output['comment_field_required'] = $this->bssHelper->isFieldRequired('required_comment');
            $output['on_which_page'] = $this->bssHelper->getDisplayAt();
            $output['action_payment_save'] = $this->bssHelper->getPaymentSaveAction();
            $output['today_date'] = $this->bssHelper->getDateToday();
            $output['min_date'] = $this->getMindate($day_off, $block_out_holidays, $process_time, $current_time);
            $output['zip_delivery_list'] = $processedZipDelivery;
            $output['orderdeliverydate_countries'] = $this->bssHelper->getCountryNotAllow();
        }
        return $output;
    }

    /**
     * Whatever we should add to day to processing day
     *
     * @return bool
     */
    public function isProcessingDayDisabled()
    {
        if (!$this->bssHelper->isAsProcessingDays()) {
            return false;
        }
        $weekDays = $this->weekdays->toOptionArray();
        $dayOff = explode(',', $this->bssHelper->getDayOff());
        $disableDayName = [];
        foreach ($weekDays as $weekDay) {
            if (isset($weekDay['value']) &&
                isset($weekDay['label']) &&
                in_array($weekDay['value'], $dayOff)) {
                $disableDayName[] = strtolower($weekDay['label']);
            }
        }
        if (in_array(strtolower($this->bssHelper->getDayOfWeekName()), $disableDayName)) {
            return true;
        }
        return false;
    }

    /**
     * Get min date
     *
     * @param string $day_off
     * @param array|string $block_out_holidays
     * @param int|string $process_time
     * @return array
     */
    protected function getMindate($day_off, $block_out_holidays, $process_time, $current_time)
    {
        // If exclude processing day = no, then return config processing time
        if (!$this->bssHelper->isAsProcessingDays()) {
            return $process_time;
        }
        // If processing time <= 0, then return config processing time
        if ($process_time <= 0) {
            return $process_time;
        }
        // If day off is empty, then return config processing time
        $dayOffArr = $this->getDayAsArray($day_off);
        $holidays = $this->getDayAsArray($block_out_holidays);
        $timeOfDayInSeconds = self::TIME_OF_DAY_IN_SECONDS;
        $tempProcessTime = $process_time;
        $tempDisabledDays = [];
        if (!empty($dayOffArr)) {
            for ($i = 0; $i < $tempProcessTime; $i++) {
                $nextOfDayInTime = $current_time + $i * $timeOfDayInSeconds;
                $momentDate = date('Y-m-d', $nextOfDayInTime);
                $momentDay = date('w', $nextOfDayInTime);
                if (in_array($momentDay, $dayOffArr)) {
                    $tempProcessTime++;
                    $tempDisabledDays[] = $momentDate;
                }
            }
        }
        if (!empty($holidays)) {
            for ($j = 0; $j < $tempProcessTime; $j++) {
                $nextOfDayInTime = $current_time + $j * $timeOfDayInSeconds;
                $momentDate = date('Y-m-d', $nextOfDayInTime);
                $momentDay = date('w', $nextOfDayInTime);
                if (in_array($momentDay, $holidays) && !in_array($momentDay, $tempDisabledDays)) {
                    $tempProcessTime ++;
                }
            }
        }

        $newTime = $tempProcessTime * $timeOfDayInSeconds + $current_time;

        return [
            'dayOfWeek' => date('w', $newTime),
            'extendedDay' => ($tempProcessTime < 7) ? 0 : ($tempProcessTime - ($tempProcessTime % 7)) / 7
        ];
    }

    /**
     * Get day as array
     *
     * @param $days
     * @return array
     */
    private function getDayAsArray($days)
    {
        return is_string($days) ? explode(',', $days) : (is_array($days) ? $days : []);
    }
}
