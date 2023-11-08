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

namespace Anowave\Ec\Plugin;

use Magento\Framework\App\Response\Http;

class Sidebar
{
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart = null;
    
    /**
     * @var \Anowave\Ec\Helper\Data
     */
    protected $dataHelper = null;
    
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;
    
    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $categoryRepository;
    
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Remove flag 
     * 
     * @var boolean
     */
    private $remove = false;
    
    /**
     * Update flag 
     * 
     * @var string
     */
    private $update = false;
    
    /**
     * Constructor
     * 
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Anowave\Ec\Helper\Data $dataHelper
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct
    (
        \Magento\Checkout\Model\Cart $cart,
        \Anowave\Ec\Helper\Data $dataHelper,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->cart = $cart;
        
        /**
         * Set helper
         *
         * @var \Anowave\Ec\Helper\Data $dataHelper
         */
        $this->dataHelper = $dataHelper;
        
        /**
         * Set product repository
         *
         * @var \Magento\Catalog\Model\ProductRepository $productRepository
         */
        $this->productRepository = $productRepository;
        
        /**
         * Set category repository
         *
         * @var \Magento\Catalog\Model\CategoryRepository $categoryRepository
         */
        $this->categoryRepository = $categoryRepository;

        /**
         * Set request 
         * 
         * @var \Magento\Framework\App\RequestInterface $request
         */
        $this->request = $request;
    }
    
    /**
     * After remove item 
     * 
     * @param \Magento\Checkout\Model\Sidebar $sidebar
     * @param \Magento\Checkout\Model\Sidebar $response
     * @return \Magento\Checkout\Model\Sidebar
     */
    public function afterRemoveQuoteItem(\Magento\Checkout\Model\Sidebar $sidebar, $response)
    {
        /**
         * Set remove
         * 
         * @var \Anowave\Ec\Plugin\Sidebar $remove
         */
        $this->remove = true;
        
        /**
         * Unset update 
         * 
         * @var \Anowave\Ec\Plugin\Sidebar $update
         */
        $this->update = false;
        
        return $response;
    }
    
    /**
     * After update item
     *
     * @param \Magento\Checkout\Model\Sidebar $sidebar
     * @param \Magento\Checkout\Model\Sidebar $response
     * @return \Magento\Checkout\Model\Sidebar
     */
    public function afterUpdateQuoteItem(\Magento\Checkout\Model\Sidebar $sidebar, $response)
    {
        /**
         * Unset remove 
         * 
         * @var \Anowave\Ec\Plugin\Sidebar $remove
         */
        $this->remove = false;
        
        /**
         * Set update
         * 
         * @var \Anowave\Ec\Plugin\Sidebar $update
         */
        $this->update = true;
        
        return $response;
    }
    
    /**
     * Get response data 
     * 
     * @param \Magento\Checkout\Model\Sidebar $sidebar
     * @param unknown $response
     * @return unknown
     */
    public function afterGetResponseData(\Magento\Checkout\Model\Sidebar $sidebar, $response)
    {
        if ($this->remove)
        {
            $item = $this->cart->getQuote()->getItemById((int) $this->request->getParam('item_id'));
            
            if ($item instanceof \Magento\Quote\Api\Data\CartItemInterface)
            {
                /**
                 * Load product
                 *
                 * @var \Magento\Catalog\Api\Data\ProductInterface $product
                 */
                $product = $this->productRepository->getById
                (
                    $item->getProductId()
                );
                
                $items = [];
                
                $item = 
                [
                    'item_id'  		=> ($this->dataHelper->useSimples() ? $this->dataHelper->getIdentifierItem($item) : $this->dataHelper->getIdentifier($product)),
                    'item_name' 	=> $item->getName(),
                    'quantity' 		=> $item->getQty(),
                    'price'			=> $item->getPriceInclTax(),
                    'base_price' 	=> $item->getProduct()->getPrice(),
                    'item_brand'    => $this->dataHelper->getBrand($product),
                    'currency'      => $this->dataHelper->getCurrency(),
                    'index'         => 0
                ];
                
                $data =
                [
                    'event' 	=> \Anowave\Ec\Helper\Constants::EVENT_REMOVE_FROM_CART,
                    'ecommerce' =>
                    [
                        'currency' => $this->dataHelper->getCurrency()
                    ]
                ];
                
                /**
                 * Get all product categories
                 */
                $categories = $this->dataHelper->getCurrentStoreProductCategories($product);
                
                /**
                 * Default list
                 * 
                 * @var string $list
                 */
                $list = null;
                
                
                if ($categories)
                {
                    /**
                     * Load last category
                     */
                    $category = $this->categoryRepository->get
                    (
                        end($categories)
                    );
                    
                    /**
                     * Assign item categori
                     */
                    $item = $this->dataHelper->assignCategory($category, $item);

                    
                    /**
                     * Get list 
                     * 
                     * @var strin $list
                     */
                    $list = $this->dataHelper->getCategoryList($category);
                    
                    /**
                     * Set list id
                     */
                    $item['item_list_id'] = $list;
                    
                    /**
                     * Set list name
                     */
                    $item['item_list_name'] = $list;
                }
                else 
                {
                    $list = __('Unlisted');
                    
                    $item['item_list_id'] = $list;
                    
                    /**
                     * Set list id
                     */
                    $item['item_list_id'] = $list;
                    
                    /**
                     * Set list name
                     */
                    $item['item_list_name'] = $list;
                }
                
                /**
                 * Update list id
                 */
                $data['ecommerce']['item_list_id'] = $list;
                
                /**
                 * Update list name
                 */
                $data['ecommerce']['item_list_name'] = $list;
                
                /**
                 * Update items
                 */
                $data['ecommerce']['items'] = 
                [
                    $item
                ];
                
                $response['remove'] = true;
                 
                /**
                 * Set response push
                 */
                $response['dataLayer'] = $data;
            }
            
            $this->remove = false;
        }
        
        if ($this->update)
        {
            $response['update'] = true;
            
            $this->update = false;
        }
        
        return $response;
    }
}