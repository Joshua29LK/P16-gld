<?php
namespace Bss\CustomBiztechDeliverydate\Plugin\Checkout\Model;

class ShippingInformationManagementPlugin
{
    protected $quoteRepository;

    protected $product;
    protected $biztechHelperData;

    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Catalog\Model\Product $product,
        \Biztech\Deliverydatepro\Helper\Data $biztechHelperData
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->product = $product;
        $this->biztechHelperData = $biztechHelperData;
    }

    /**
     * Set extension attribute
     *
     * @param $subject
     * @param $cartId
     * @param $addressInformation
     * @return false
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeSaveAddressInformation($subject, $cartId, $addressInformation) {
        try {
            $quote = $this->quoteRepository->getActive($cartId);
            $shippingArrivalDate = $quote->getShippingArrivalDate();
            $highestDeliveryDate = $this->getFinalDeliveryDate($quote);

            if (date('Y-m-d', strtotime($shippingArrivalDate)) == date('Y-m-d', strtotime($highestDeliveryDate))) {
                $quote->setIsFastestDate(true);
            } else {
                $quote->setIsFastestDate(false);
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * Copy func
     *
     * @param $quote
     * @return void
     */
    public function getFinalDeliveryDate($quote)
    {
        $highestDeliveryDate = null;
        $items = $quote->getAllItems();
        $deliveryDayNumbers = [];
        $deliveryDate = [];
        foreach($items as $item) {
            $productId = $item->getProductId();
            $product = $this->product->load($productId);
            $deliveryDate[] = $this->biztechHelperData->getFinalDeliveryDate($product);
            $deliveryDays = $product->getDeliveryDays();
            if (is_array($deliveryDays)) {
                $deliveryDayNumbers = $deliveryDays;
            } elseif (is_string($deliveryDays)) {
                $deliveryDayNumbers = explode(',', $deliveryDays);
            } else {
                $deliveryDayNumbers = null;
            }
            $deliveryDayNumbers[] = $deliveryDayNumbers;
        }
        if (is_array($deliveryDayNumbers) && !empty($deliveryDayNumbers)) {
            // Filter out null or empty arrays
            $validArrays = array_filter($deliveryDayNumbers, function ($element) {
                return is_array($element) && !empty($element);
            });

            if (!empty($validArrays)) {
                if (count($validArrays) === 1) {
                    // If there's only one valid array, return it directly
                    $commonDays = array_values($validArrays)[0];
                } else {
                    // Call array_intersect with the valid arrays
                    $commonDays = call_user_func_array('array_intersect', $validArrays);
                }
            } else {
                // Handle the case where no valid arrays are left after filtering
                $commonDays = [];
            }
        }
        if (is_array($deliveryDate) && !empty($deliveryDate)) {
            if(count($deliveryDate) > 0) {
                $highestDeliveryDate = $this->findHeighestDeliverydate($deliveryDate,$commonDays);
            }
        }
        return $highestDeliveryDate;
    }

    /**
     * Copy func
     *
     * @param $deliveryDate
     * @param $commonDays
     * @return mixed|string|null
     * @throws \DateMalformedStringException
     */
    public function findHeighestDeliverydate($deliveryDate,$commonDays)
    {
        $highestDeliveryDate = null;
        foreach($deliveryDate as $delivery)
        {
            if($highestDeliveryDate == null || $delivery > $highestDeliveryDate)
            {
                $highestDeliveryDate = $delivery;
            }
        }

        if (empty($commonDays)) {
            return $highestDeliveryDate;
        }

        $date = new \DateTime($highestDeliveryDate);

        sort($commonDays);

        // Get the numeric representation of the current day of the week (0 for Sunday, 1 for Monday, ..., 6 for Saturday)
        $currentDayNum = $date->format('w');

        foreach ($commonDays as $dayNum) {
            if ($dayNum >= $currentDayNum) {
                // Set the date to the next occurrence of the common day
                $daysUntilNextCommonDay = $dayNum - $currentDayNum;
                $date->modify("+{$daysUntilNextCommonDay} days");
                return $date->format('Y-m-d');
            }
        }

        // If no common day is found after the highest delivery date, move to the first common day of the next week
        $daysUntilNextWeekCommonDay = (7 - $currentDayNum) + $commonDays[0];
        $date->modify("+{$daysUntilNextWeekCommonDay} days");
        return $date->format('Y-m-d');
    }
}
