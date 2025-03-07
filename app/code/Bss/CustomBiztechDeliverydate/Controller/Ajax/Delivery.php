<?php
namespace Bss\CustomBiztechDeliverydate\Controller\Ajax;

class Delivery extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;

    protected $helperDeliverydate;

    protected $helperDeliverydatepro;

    protected $localeResolver;

    protected $product;

    protected $request;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Biztech\Deliverydate\Helper\Data $helperDeliverydate,
        \Biztech\Deliverydatepro\Helper\Data $helperDeliverydatepro,
        \Magento\Framework\Locale\Resolver $localeResolver,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\App\RequestInterface $request
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helperDeliverydate = $helperDeliverydate;
        $this->helperDeliverydatepro = $helperDeliverydatepro;
        $this->localeResolver = $localeResolver;
        $this->product = $product;
        $this->request = $request;
    }

    /**
     * Get delivery date with product id
     *
     * @return mixed
     */
    public function execute()
    {
        $result = '';
        $jsonFactory = $this->resultJsonFactory->create();
        $productId = (int)$this->request->getParam('product_id');
        $currentProduct = $this->product->load($productId);

        if (!$currentProduct->getId()) {
            return $jsonFactory->setData([
                'delivery_date' => $result
            ]);
        }

        // Copy logic Block Biztech_Deliverydatepro
        $format = $this->helperDeliverydate->getDeliveryDateFormat();
        $deliveryDateConfig = $this->helperDeliverydatepro->getFinalDeliveryDate($currentProduct);
        $deliveryDate = isset($deliveryDateConfig) ? $deliveryDateConfig : '';
        $translatedDate = $this->getTranslatedDate($deliveryDate);
        $formattedDeliveryDate = !empty($deliveryDate) ? date($format, strtotime($deliveryDate)) : '';

        if (!empty($formattedDeliveryDate)) {
            // Translate the date first
            $getTranslatedDeliveryDate = $this->getTranslatedDate($deliveryDate);

            // Split the translated date into parts (first word and the rest of the string)
            $parts = explode(' ', $getTranslatedDeliveryDate, 2);
            $firstWord = $parts[0];  // The first word (weekday)
            $restOfString = isset($parts[1]) ? $parts[1] : '';  // The rest of the date


            $result = ucfirst($firstWord) . ' ' . $restOfString . ' bezorgd';
        }

        return $jsonFactory->setData([
            'delivery_date' => $result
        ]);
    }

    /**
     * Copy func
     *
     * @param $deliveryDate
     * @return false|string
     */
    public function getTranslatedDate($deliveryDate)
    {
        $locale = $this->localeResolver->getLocale(); // e.
        $timestamp = strtotime($deliveryDate);  // Adjust if your format differs

        // Create an IntlDateFormatter for the current locale
        $formatter = new \IntlDateFormatter(
            $locale,
            \IntlDateFormatter::FULL, // Full format for day and month names
            \IntlDateFormatter::NONE, // No time formatting
            null,                     // Use the default timezone
            \IntlDateFormatter::GREGORIAN, // Use Gregorian calendar
            'EEEE d MMMM'  // Format: Full day name, day, full month name
        );

        // Return the date formatted into the locale language
        $transDate = $formatter->format($timestamp);
        return $transDate;
    }
}
