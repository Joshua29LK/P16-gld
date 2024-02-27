<?php
/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Ec
 * @copyright 	Copyright (c) 2023 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Ec4\Plugin;

use Magento\Framework\App\Response\Http;
use Magento\Framework\Event\Observer as EventObserver;

class Refund
{
    /**
     * No category fallback 
     * 
     * @var string
     */
    const FALLBACK_NO_CATEGORY = 'Not set';
    
    /**
     * @var \Anowave\Ec4\Helper\Data
     */
    protected $helper = null;
    
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager = null;
    
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    
    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $categoryRepository;
    
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory
     */
    protected $attribute;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager = null;
    
    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;
    
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    
    /**
     * @var \Anowave\Ec\Model\Logger
     */
    protected $logger;
    
    /**
     * Constructor 
     * 
     * @param \Anowave\Ec4\Helper\Data $helper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
     * @param \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attribute
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\State $state
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Anowave\Ec\Model\Logger $logger
     */
    public function __construct
    (
        \Anowave\Ec4\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attribute,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\State $state,
        \Magento\Framework\App\RequestInterface $request,
        \Anowave\Ec\Model\Logger $logger
    )
    {
        /**
         * Set helper
         *
         * @var \Anowave\Ec4\Helper\Data $helper
         */
        $this->helper = $helper;
        
        /**
         * Set message manager
         *
         * @var \Magento\Framework\Message\ManagerInterface $_messageManager
         */
        $this->messageManager = $messageManager;
        
        /**
         * Set store manager
         *
         * @var \Magento\Store\Model\StoreManagerInterface $storeManager
         */
        $this->storeManager = $storeManager;
        
        /**
         * Set product factory
         *
         * @var \Magento\Catalog\Model\ProductFactory $productFactory
         */
        $this->productFactory = $productFactory;
        
        /**
         * Set attribute
         *
         * @var \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory
         */
        $this->attribute = $attribute;
        
        /**
         * Set category repository
         *
         * @var \Magento\Catalog\Model\CategoryRepository $categoryRepository
         */
        $this->categoryRepository = $categoryRepository;
        
        /**
         * Set scope config
         *
         * @var \Anowave\Ec\Observer\Refund $scopeConfig
         */
        $this->scopeConfig = $scopeConfig;
        
        /**
         * Set state 
         * 
         * @var \Magento\Framework\App\State $state
         */
        $this->state = $state;
        
        /**
         * Set request 
         * 
         * @var \Anowave\Ec4\Plugin\Refund $request
         */
        $this->request = $request;
        
        /**
         * Set logger 
         * 
         * @var \Anowave\Ec\Model\Logger $logger
         */
        $this->logger = $logger;
    }
    
    /**
     * Modify exeucte method 
     * 
     * @param \Anowave\Ec\Observer\Refund $context
     * @param callable $proceed
     * @param EventObserver $observer
     * @return unknown|boolean
     */
    public function aroundExecute(\Anowave\Ec\Observer\Refund $context, callable $proceed, EventObserver $observer)
    {
        if (!$this->trackRefund())
        {
            return $proceed($observer);
        }
        
        /**
         * Get credit memo details 
         * 
         * @var array $creditmemo
         */
        $creditmemo = $this->request->getParam('creditmemo');
       
        if ($this->refund($observer->getPayment()->getOrder(), $creditmemo))
        {
            $store = (int) $observer->getPayment()->getOrder()->getStoreId();
            
            $this->messageManager->addSuccessMessage("Refund for order {$observer->getPayment()->getOrder()->getIncrementId()} tracked to Google Analytics 4 {$this->helper->getMeasurementId($store)} successfully.");
            
            return true;
        }
        
        return $proceed($observer);
    }
    
    /**
     * Refund order 
     * 
     * @param \Magento\Sales\Model\Order $order
     * @return boolean
     */
    public function refund(\Magento\Sales\Model\Order $order, array $creditmemo = [])
    {
        /**
         * Get store 
         * 
         * @var int $store
         */
        $store = (int) $order->getStoreId();

        /**
         * Get measurement id 
         * 
         * @var string $measurement_id
         */
        $measurement_id = $this->helper->getMeasurementId($store);
        
        /**
         * Get measurement secret
         * 
         * @var string $measurement_api_secret
         */
        $measurement_api_secret = $this->helper->getMeasurementApiSecret($store);
        
        /**
         * Get payload
         * 
         * @var \Closure $payload
         */
        $payload = function(array $events = []) use ($measurement_id, $measurement_api_secret)
        {
            return
            [
                'client_id' => bin2hex(random_bytes(32)),
                'events'    => $events
            ];
        };
        
        if ($measurement_id && $measurement_api_secret)
        {
            $items = [];
            
            /**
             * Default start position
             *
             * @var int
             */
            $index = 1;
            
            if ($order->getTotalRefunded() > 0)
            {
                if ($order->getIsVirtual())
                {
                    $address = $order->getBillingAddress();
                }
                else
                {
                    $address = $order->getShippingAddress();
                }
                
                $products = $this->getProducts($order, $creditmemo);
                
                foreach($products as $product)
                {
                    $item =
                    [
                        'index'         =>          $index,
                        'item_id'       =>          @$product['id'],
                        'item_name'     =>          @$product['name'],
                        'item_brand'    => (string) @$product['brand'],
                        'price'         => (float)  @$product['price'],
                        'quantity'      => (int)    @$product['quantity']
                    ];
                    
                    $categories = explode(chr(47), (string) @$product['category']);
                    
                    if ($categories)
                    {
                        $category = array_shift($categories);
                        
                        if ($category)
                        {
                            $item['item_category'] = $category;
                        }
                        
                        foreach ($categories as $index => $category)
                        {
                            $key = $index + 2;
                            
                            $item["item_category{$index}"] = $category;
                        }
                    }
                    else 
                    {
                        $item['item_category'] = __(static::FALLBACK_NO_CATEGORY);
                    }
                    
                    
                    $items[] = $item;
                    
                    $index++;
                }
                
                $data = $payload
                (
                    [
                        [
                            'name' => 'refund',
                            'params' =>
                            [
                                'currency'       => $this->helper->getBaseHelper()->getCurrency(),
                                'transaction_id' => $order->getIncrementId(),
                                'value'          => (float) $this->helper->getBaseHelper()->getRevenue($order),
                                'shipping'	     => (float) $order->getShippingInclTax(),
                                'tax'	         => (float) $order->getTaxAmount(),
                                'affiliation'    => $this->helper->getBaseHelper()->escape
                                (
                                    $this->helper->getBaseHelper()->getStoreName()
                                ),
                                'items' => $items,
                                'traffic_type' => $this->state->getAreaCode()
                            ]
                        ]
                    ]
                );

                $analytics = curl_init("https://www.google-analytics.com/mp/collect?measurement_id={$measurement_id}&api_secret={$measurement_api_secret}");
                
                curl_setopt($analytics, CURLOPT_HEADER, 		0);
                curl_setopt($analytics, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($analytics, CURLOPT_POST, 			1);
                curl_setopt($analytics, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($analytics, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($analytics, CURLOPT_USERAGENT,		'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
                curl_setopt($analytics, CURLOPT_POSTFIELDS,     mb_convert_encoding(json_encode($data, JSON_UNESCAPED_UNICODE), 'UTF-8', mb_list_encodings()));

                try
                {
                    $response = curl_exec($analytics);
                    
                    if ($this->helper->getBaseHelper()->useDebugMode())
                    {
                        $this->messageManager->addNoticeMessage(json_encode($data, JSON_PRETTY_PRINT));
                    }
                     
                    if (!curl_error($analytics))
                    { 
                        $this->logger->log(json_encode($data));
                        
                        return true;
                    } 
                }
                catch (\Exception $e) 
                {
                    $this->messageManager->addErrorMessage($e->getMessage());
                    
                    $this->logger->log($e->getMessage());
                }
            }
        }
        
        return false;
    }
    
    /**
     * Get products 
     * 
     * @param \Magento\Sales\Model\Order $order
     * @param array $creditmemo
     * @return array
     */
    public function getProducts(\Magento\Sales\Model\Order $order, array $creditmemo = []) : array 
    {
        /**
         * Get products 
         * 
         * @var array $products
         */
        $products = [];
        
        /**
         * Map 
         * 
         * @var array $map
         */
        $map = [];
        
        foreach ($creditmemo['items'] as $identifier => $entity)
        {
            if ((int) $entity['qty'] > 0)
            {
                $map[(int) $identifier] = (int) $entity['qty'];
            }
        }

        foreach ($order->getAllVisibleItems() as $item)
        {
            if (array_key_exists($item->getId(), $map))
            {
                $collection = [];
                
                if ($item->getProduct())
                {
                    $entity = $this->productFactory->create()->load
                    (
                        $item->getProduct()->getId()
                    );
                    
                    $collection = $entity->getCategoryIds();
                }
                
                if ($collection)
                {
                    $category = $this->categoryRepository->get(end($collection));
                }
                else
                {
                    $category = null;
                }
                
                /**
                 * Get product name
                 */
                $args = new \stdClass();
                
                $args->id 	= $this->helper->getBaseHelper()->getIdentifierItem($item);
                $args->name = $item->getName();
                
                /**
                 * Product variant(s)
                 *
                 * @var []
                 */
                $variant = [];
                
                if ('configurable' === $item->getProductType())
                {
                    $options = (array) $item->getProductOptions();
                    
                    if (isset($options['info_buyRequest']))
                    {
                        $info = new \Magento\Framework\DataObject($options['info_buyRequest']);
                        
                        /**
                         * Construct variant
                         */
                        foreach ((array) $info->getSuperAttribute() as $id => $option)
                        {
                            /**
                             * Load attribute model
                             *
                             * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
                             */
                            $attribute = $this->attribute->create()->load($id);
                            
                            if ($attribute->usesSource())
                            {
                                $variant[] = join(\Anowave\Ec\Helper\Data::VARIANT_DELIMITER_ATT,
                                [
                                    $this->escape($attribute->getFrontendLabel()),
                                    $this->escape($attribute->getSource()->getOptionText($option))
                                ]);
                            }
                        }
                    }
                }
                
                $data =
                [
                    'name' 		=> $this->escape($args->name),
                    'id'		=> $this->escape($args->id),
                    'price' 	=> $item->getPrice(),
                    'quantity' 	=> $map[$item->getId()],
                    'variant'	=> join(\Anowave\Ec\Helper\Data::VARIANT_DELIMITER, $variant)
                ];
                
                if ($category)
                {
                    $data['category'] = $this->escape($category->getName());
                }
                
                $products[] = $data;
            }
        }
        
        return $products;
    }
    
    /**
     * Escape quotes
     *
     * @param string $string
     */
    public function escape($data)
    {
        return $this->helper->getBaseHelper()->escape($data);
    }
    
    /**
     * Check if refund tracking is enabled
     *
     * @return boolean
     */
    public function trackRefund() : bool
    {
        return 1 === (int) $this->helper->getConfig('ec/gmp/use_measurement_protocol_refund');
    }
}