<?php

namespace Ofzio\BakaGlass\Observer;

use GraphQL\Client;
use GraphQL\Mutation;
use GraphQL\RawObject;
use Magento\Catalog\Model\Product\Option;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

// require_once 'vendor/autoload.php';

class ProductSaveAfter implements ObserverInterface
{
  /**
   * @var ScopeConfigInterface
   */
  protected $scopeConfig;

  public function __construct(
    ScopeConfigInterface $scopeConfig
  ) {
    $this->scopeConfig = $scopeConfig;
  }

  private function mutateProduct(Client $client, string $oldSku, $product)
  {
    // \Sentry\init(['dsn' => 'https://95ca071948504082a025177b4a088138@o770448.ingest.sentry.io/4504553529671680' ]);

    $mutation = (new Mutation('product'))
      ->setArguments([
        'input' => new RawObject('{
            sellingPrice: ' . $product->getData('price') . '
            description: "' . $product->getData('name') . '"
            articleNumber: "' . $product->getData('sku') . '"
        }'),
        'existingArticleNumber' => $oldSku
      ])
      ->setSelectionSet(
        [
          'id'
        ]
      )
    ;

    try {
      $client->runQuery($mutation);
    } catch (\Exception $e) {
    }
  }

  public function execute(EventObserver $observer)
  {
    try {
      $product = $observer->getProduct();

      $apiKey = $this->scopeConfig->getValue('settings/general/apikey', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
      $client = new Client(
        // 'https://localhost:8000/graphql/public_v1',
        'https://glasdiscount.bakaglass.nl/graphql/public_v1',
        ['X-API-TOKEN' => $apiKey]
      );

      $oldSku = $product->getOrigData('sku');
      $this->mutateProduct($client, $oldSku, $product);
    } catch (\Throwable $exception) {
      // \Sentry\captureException($exception);
    }

    // foreach ($product->getOptions() as $option) {
    //   /**
    //    * @var Option
    //    */
    //   $option = $option;
    //   foreach ($option->getValuesCollection() as $value) {
    //     $oldOptionSku = $value->getOrigData('sku');
    //     if ($oldOptionSku) $this->mutateProduct($client, $oldOptionSku, $basePrice + $value->getData('price'));
    //   }
    // }
  }
}