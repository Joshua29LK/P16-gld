<?php

namespace Ofzio\BakaGlass\Observer;

use GraphQL\Client;
use GraphQL\Mutation;
use GraphQL\RawObject;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Psr\Log\LoggerInterface;

// require_once 'vendor/autoload.php';

class OrderPlaceAfter implements ObserverInterface
{
  /**
   * @var ScopeConfigInterface
   */
  protected $scopeConfig;

  /**
   * @var LoggerInterface
   */
  protected $logger;

  public function __construct(
    ScopeConfigInterface $scopeConfig,
    LoggerInterface $logger
  ) {
    $this->scopeConfig = $scopeConfig;
    $this->logger = $logger;
  }

  public function execute(EventObserver $observer)
  {
    // \Sentry\init(['dsn' => 'https://95ca071948504082a025177b4a088138@o770448.ingest.sentry.io/4504553529671680' ]);

    try {
      /**
       * @var Order $order
       */
      $order = $observer->getEvent()->getOrder();

      $apiKey = $this->scopeConfig->getValue('settings/general/apikey', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
      $client = new Client(
        // 'https://localhost:8000/graphql/public_v1',
        'https://glasdiscount.bakaglass.nl/graphql/public_v1',
        ['X-API-TOKEN' => $apiKey]
      );

      $items = $order->getAllVisibleItems();

      $lazyRowsString = '';
      foreach ($items as $item) {
        $width = '1000';
        $height = '1000';
        $remark = '';

        $foundWidth = false;
        $foundHeight = false;
        $foundLength = false;

        /**
         * @var Item
         */
        $item = $item;

        $cavity = null;

        $publicSubrowsString = '';

        $options = $item->getProductOptions();
        $articleNumber = array_key_exists('simple_sku', $options) ? $options['simple_sku'] : $item->getSku();    
        if (isset($options['options']) && !empty($options['options'])) {
          foreach ($options['options'] as $option) {
            if (str_contains(strtolower($option['label']), 'spouw')) {
              $optionData = $item->getProduct()->getOptionById($option['option_id']);
              $value = $optionData->getValueById($option['option_value']);
              $title = $value->getTitle();
              $cavity = explode(' ', $title)[0];
            } else if (str_contains(strtolower($option['label']), 'lengte') && !$foundLength) {
              $optionData = $item->getProduct()->getOptionById($option['option_id']);
              $value = $optionData->getValueById($option['option_value']);
              $title = $value->getTitle();
              $width = explode(' ', $title)[0];
            } else if (str_contains(strtolower($option['label']), 'breedte') && !$foundWidth) {
              $width = $option['option_value'];
              $foundWidth = true;
            } else if (str_contains(strtolower($option['label']), 'hoogte') && !$foundHeight) {
              $height = $option['option_value'];
              $foundHeight = true;
            } else if (
              str_contains(strtolower($option['label']), 'glassoort') ||
              str_contains(strtolower($option['label']), 'samenstelling')
            ) {
              $optionData = $item->getProduct()->getOptionById($option['option_id']);
              $value = $optionData->getValueById($option['option_value']);
              $articleNumber = $value->getSku();
            } else if (str_contains(strtolower($option['label']), 'kleur kader')) {
              $optionData = $item->getProduct()->getOptionById($option['option_id']);
              $value = $optionData->getValueById($option['option_value']);

              $publicSubrowsString .= '
                {
                  articleNumber: "' . $value->getSku() . '"
                }
              ';
            } else {
              if ($remark !== '') $remark .= ', ';
              $remark .= $option['label'] . ': ' . $option['option_value'] . '';
            }
          }
        }

        if ($articleNumber) {
          $lazyRowsString .= '
            {
              amount: ' . $item->getQtyOrdered() . '
              articleNumber: "' . $articleNumber . '"
              width: ' . $width . '
              height: ' . $height . '
              ' . ($cavity ? ('cavity: ' . $cavity) : '') . '
              subRows: [' . $publicSubrowsString . ']
              description: "' . $item->getName() . '"
              fixedPrice: ' . $item->getPrice() . '
              remark: "' . $remark . '"
            }
          ';
        }
      }

      $shippingAddress = $order->getShippingAddress();
      if (!$shippingAddress) {
        $shippingAddress = $order->getBillingAddress();
      }

      $isDeliver = !str_contains($order->getShippingDescription(), 'Afhalen');

      $streetNumber = '';
      $addition = '';

      if (array_key_exists(1, $shippingAddress->getStreet())) {
        $streetNumber = $shippingAddress->getStreet()[1];
      }

      if (array_key_exists(2, $shippingAddress->getStreet())) {
        $addition = $shippingAddress->getStreet()[2];
      }

      $firstName = $order->getCustomerFirstname();
      $lastName = $order->getCustomerLastname();
      if ($order->getCustomerIsGuest()) {
        $firstName = $shippingAddress->getFirstname();
        $lastName = $shippingAddress->getLastname();
      }

      $companyName = $shippingAddress->getCompany();

      $mutation = (new Mutation('order'))
        ->setArguments(['input' => new RawObject('{
          type: ' . ($isDeliver ? 'DELIVER' : 'PICK_UP') . '
          ' . ($companyName ? ('companyName: "' . $companyName . '"') : '') . '
          ' . ($firstName ? ('firstName: "' . $firstName . '"') : '') . '
          ' . ($lastName ? ('lastName: "' . $lastName . '"') : '') . '
          email: "' . $order->getCustomerEmail() . '"
          address: {
            street: "' . $shippingAddress->getStreet()[0] .  '"
            streetNumber: "' . $streetNumber . '"
            addition: "' . $addition . '"
            zipCode: "' . $shippingAddress->getPostcode() . '"
            city: "' . $shippingAddress->getCity() . '"
            state: "' . $shippingAddress->getRegion() . '"
            country: "' . $shippingAddress->getCountryId() . '"
          }
          lazyRows: [' . $lazyRowsString . ']
          internalMemo: "' . $order->getCustomerNote() . '"
          phoneNumber: "' . $shippingAddress->getTelephone() . '"
      }')])
        ->setSelectionSet(
          [
              'id'
          ]
        )
      ;
      $client->runQuery($mutation);
    } catch (\Throwable $exception) {
      $this->logger->error($exception->getMessage(), [
        'exception' => $exception,
      ]);
    }
  }
}