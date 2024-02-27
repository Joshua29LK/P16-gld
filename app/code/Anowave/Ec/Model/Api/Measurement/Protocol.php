<?php
/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * https://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Ec
 * @copyright 	Copyright (c) 2023 Anowave (https://www.anowave.com/)
 * @license  	https://www.anowave.com/license-agreement/
 */

namespace Anowave\Ec\Model\Api\Measurement;

class Protocol
{
	/**
	 * Client ID
	 *
	 * @var UUID
	 */
	protected $cid = null;
	
	/**
	 * Google Analytics 4 session id
	 * 
	 * @var UUID
	 */
	protected $gsid = null;
	
	/** 
	 * @var \Anowave\Ec\Helper\Data
	 */
	protected $helper = null;
	
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;
	
	/**
	 * @var \Magento\Catalog\Model\ProductFactory
	 */
	protected $productFactory;
	
	/**
	 * @var \Magento\Catalog\Model\CategoryRepository
	 */
	protected $categoryRepository;
	
	/**
	 * @var \Magento\Sales\Model\OrderFactory
	 */
	protected $orderFactory;
	
	/**
	 * @var \Magento\Framework\Session\SessionManagerInterface
	 */
	protected $session = null;
	
	/**
	 * @var \Magento\Config\Model\ResourceModel\Config
	 */
	protected $resourceConfig;
	
	/**
	 * Errors array 
	 * 
	 * @var array
	 */
	protected $errors = [];
	
	/**
	 * @var \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory
	 */
	protected $attribute;
	
	/**
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $scopeConfig;
	
	/**
	 * @var \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory
	 */
	protected $transactionFactory;
	
	/**
	 * @var \Anowave\Ec\Model\Random
	 */
	protected $random;
	
	/**
	 * @var \Anowave\Ec\Model\Cookie\Analytics
	 */
	protected $cookie;
	
	/**
	 * @var \Anowave\Ec\Model\Cookie\AnalyticsSession
	 */
	protected $cookie_session;
	
	/**
	 * Constructor 
	 * 
	 * @param \Anowave\Ec\Helper\Data $helper
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Catalog\Model\ProductFactory $productFactory
	 * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
	 * @param \Magento\Sales\Model\OrderFactory $orderFactory
	 * @param \Magento\Framework\Session\SessionManagerInterface $session
	 * @param \Magento\Config\Model\ResourceModel\Config $resourceConfig
	 * @param \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attribute
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	 * @param \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory $transactionFactory
	 * @param \Anowave\Ec\Model\Random $random
	 * @param \Anowave\Ec\Model\Cookie\Analytics $cookie
	 * @param \Anowave\Ec\Model\Cookie\AnalyticsSession $cookie_session
	 */
	public function __construct
	(
		\Anowave\Ec\Helper\Data $helper,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Catalog\Model\CategoryRepository $categoryRepository,
		\Magento\Sales\Model\OrderFactory $orderFactory,
		\Magento\Framework\Session\SessionManagerInterface $session,
		\Magento\Config\Model\ResourceModel\Config $resourceConfig,
		\Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attribute,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
	    \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory $transactionFactory,
	    \Anowave\Ec\Model\Random $random,
	    \Anowave\Ec\Model\Cookie\Analytics $cookie,
	    \Anowave\Ec\Model\Cookie\AnalyticsSession $cookie_session
	)
	{
		/**
		 * Set helper 
		 * 
		 * @var \Anowave\Ec\Helper\Data
		 */
		$this->helper = $helper;
		
		/**
		 * Set store manager 
		 * 
		 * @var \Magento\Store\Model\StoreManagerInterface
		 */
		$this->storeManager = $storeManager;
		
		/**
		 * Set product factory 
		 * 
		 * @var \Magento\Catalog\Model\ProductFactory
		 */
		$this->productFactory = $productFactory;
		
		/**
		 * Set category repository 
		 * 
		 * @var \Magento\Catalog\Model\CategoryRepository $categoryRepository
		 */
		$this->categoryRepository = $categoryRepository;
		
		/**
		 * Set order factory 
		 * 
		 * @var \Magento\Sales\Model\OrderFactory
		 */
		$this->orderFactory = $orderFactory;
		
		/**
		 * Set session 
		 * 
		 * @var \Magento\Framework\Session\SessionManagerInterface
		 */
		$this->session = $session;
		
		/**
		 * Set resource config
		 * 
		 * @var \Magento\Config\Model\ResourceModel\Config
		 */
		$this->resourceConfig = $resourceConfig;
		
		/**
		 * Set attribute factory 
		 * 
		 * @var \Anowave\Ec\Model\Api\Measurement\Protocol $attribute
		 */
		$this->attribute = $attribute;
		
		/**
		 * Set scope config
		 * 
		 * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
		 */
		$this->scopeConfig = $scopeConfig;
		
		/**
		 * Set transaction factory 
		 * 
		 * @var \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory $transactionFactory
		 */
		$this->transactionFactory = $transactionFactory;
		
		/**
		 * Set random model
		 * 
		 * @var \Anowave\Ec\Model\Api\Measurement\Protocol $random
		 */
		$this->random = $random;
		
		/**
		 * Set cookie model 
		 * 
		 * @var \Anowave\Ec\Model\Cookie\Analytics $cookie
		 */
		$this->cookie = $cookie;
		
		/**
		 * Set analytics session cookie model 
		 * 
		 * @var \Anowave\Ec\Model\Cookie\AnalyticsSession $cookie_session
		 */
		$this->cookie_session = $cookie_session;
	}
	
	/**
	 * Track order by id
	 * 
	 * @param int $id
	 */
	public function purchaseById($id = null)
	{
		$order = $this->orderFactory->create()->load($id);
		
		if ($order && $order->getId())
		{
			return $this->purchase($order);
		}
		
		return false;
	}
	
	/**
	 * Track order
	 * 
	 * @param \Magento\Sales\Model\Order $order
	 * @param string $reverse
	 * @throws \Exception
	 * @return \Anowave\Ec\Model\Api\Measurement\Protocol
	 */
	public function purchase(\Magento\Sales\Model\Order $order, $reverse = false)
	{
		return $this;
	}
	
	/**
	 * Cancel and reverse order 
	 * 
	 * @param \Magento\Sales\Model\Order $order
	 */
	public function cancel(\Magento\Sales\Model\Order $order)
	{
	    /**
	     * Reset errors
	     * 
	     * @var \Anowave\Ec\Model\Api\Measurement\Protocol $errors
	     */
	    $this->errors = [];
	    
	    try 
	    {
	        $collection = $this->transactionFactory->create()->addFieldToFilter('ec_order_id', $order->getId())->addFieldToFilter('ec_track', \Anowave\Ec\Helper\Constants::FLAG_TRACKED);
    	    
    	    /**
    	     * Check if order is found inside the ae_ec table
    	     */
    	    if (!$collection->getSize())
    	    {
    	        $this->errors[] = __('Reverse transaction to Google Analytics skipped.');
    	        
    	        return false;
    	    }
	    }
	    catch (\Exception $e)
	    {
	        $this->errors[] = $e->getMessage();
	    }
	    
	    /**
	     * Reverse transaction
	     */
	    $this->purchase($order, true);
	    
	    /**
	     * Check errors
	     */
		if ($this->errors)
		{
			return false;
		}
		
		return true;
	}
	
	
	/**
	 * Get UA-ID
	 *
	 * @param \Magento\Sales\Model\Order $order
	 * @return string
	 */
	public function getUA(\Magento\Sales\Model\Order $order = null)
	{
		return null;
	}
	
	/**
	 * Get Client ID
	 *
	 * @var UUID
	 */
	public function getCID()
	{
		if (!$this->cid)
		{
		    if (null !== $cid = $this->cookie->get())
		    {
		        $this->cid = $cid;
		    }
		    else 
		    {
		        if (null !== $cid = $this->session->getCID())
		        {
		            $this->cid = $cid;
		        }
		        else 
		        {
		            $this->cid = $this->random->getCID();
		            
		            $this->session->setCID($this->cid);
		        }
		    }
		}

		return $this->cid;
	}
	
	/**
	 * Get Google Session ID
	 * 
	 * @param string $measurement_id
	 * @return \Anowave\Ec\Model\Api\Measurement\UUID
	 */
	public function getGSID(string $measurement_id = '')
	{
	    if (!$this->gsid)
	    {
	        if (null !== $gsid = $this->cookie_session->get($measurement_id))
	        {
	            $this->gsid = $gsid;
	        }
	        else 
	        {
	            if (null !== $gsid = $this->session->getGSID())
	            {
	                $this->gsid = $gsid;
	            }
	            else
	            {
	                $this->gsid = $this->random->getCID();
	                
	                $this->session->setGSID($this->gsid);
	            }
	        }
	    }
	    
	    return $this->gsid;
	}
	
	/**
	 * Set client id 
	 * 
	 * @param string $cid
	 * @return \Anowave\Ec\Model\Api\Measurement\Protocol
	 */
	public function setCID($cid = null)
	{
	    if ($cid)
	    {
	       $this->cid = $cid;
	    }
	    
	    return $this;
	}
	
	/**
	 * Get order products array
	 *
	 * @param Mage_Sales_Model_Order $order
	 * @return []
	 */
	public function getProducts(\Magento\Sales\Model\Order $order = null)
	{
		/**
		 * Order products array
		 * 
		 * @var []
		 */
		$products = [];
		
		if ($order->getIsVirtual())
		{
			$address = $order->getBillingAddress();
		}
		else
		{
			$address = $order->getShippingAddress();
		}

		foreach ($order->getAllVisibleItems() as $item)
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
				
			$args->id 	= $this->helper->getIdentifierItem($item);
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
						 * Load attribute 
						 * 
						 * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
						 */
						$attribute = $this->attribute->create()->load($id);
						
						if ($attribute->usesSource())
						{
							$variant[] = join(\Anowave\Ec\Helper\Data::VARIANT_DELIMITER_ATT, array
							(
								$this->helper->escape($attribute->getFrontendLabel()),
								$this->helper->escape($attribute->getSource()->getOptionText($option))
							));
						}
					}
				}
			}

			$data = 
			[
				'name' 		=> $this->helper->escape($args->name),
				'id'		=> $this->helper->escape($args->id),
				'price' 	=> $item->getPrice(),
				'quantity' 	=> $item->getQtyOrdered(),
				'variant'	=> join(\Anowave\Ec\Helper\Data::VARIANT_DELIMITER, $variant)
			];
			
			if ($category)
			{
				$data['category'] = $this->helper->escape($category->getName());
			}
			
			$products[] = $data;
		}

		return $products;
	}
	
	
	public function fallback(array $params = [])
	{
	    return false;
	}
	
	
	/**
	 * Get root category id
	 *
	 * @param unknown $store
	 * @throws \Exception
	 */
	public function getStoreRootCategoryId($store)
	{
		if (is_int($store))
		{
			$store = $this->storeManager->getStore($store);
		}
	
		if (is_string($store))
		{
			foreach ($this->storeManager->getStores() as $model)
			{
				if ($model->getCode() == $store)
				{
					$store = $model;
						
					break;
				}
			}
		}
	
		if ($store instanceof \Magento\Store\Model\Store)
		{
			return $store->getRootCategoryId();
		}
	
		throw new \Exception("Store $store does not exist anymore");
	}
	
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Build variant parameter
	 *
	 * @var [] $variant
	 * @return string
	 */
	protected function getVariant($variant = array())
	{
		return join(\Anowave\Ec\Helper\Data::VARIANT_DELIMITER, $variant);
	}
}