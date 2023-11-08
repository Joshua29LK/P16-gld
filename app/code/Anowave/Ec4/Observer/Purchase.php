<?php
/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking GA4
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
 * @package 	Anowave_Ec4
 * @copyright 	Copyright (c) 2023 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Ec4\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class Purchase implements ObserverInterface
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;
    
    /**
     * Constructor 
     * 
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     */
    public function __construct
    (
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    )
    {
        $this->orderCollectionFactory = $orderCollectionFactory;
    }
    
    /**
     * Add order information into GA block to render on checkout success pages
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        /**
         * Get payload response
         * 
         * @var array $response
         */
        $response = $observer->getTransport()->getResponse();
        
        /**
         * Get order
         * 
         * @var \Magento\Sales\Model\Order $order
         */
        $order = $observer->getTransport()->getOrder();
        
        /**
         * Get email
         * 
         * @var string $email
         */
        $email = $order->getCustomerEmail();
        
        if ($email)
        {
            $collection = $this->orderCollectionFactory->create()->addFieldToSelect('*');
            
            /**
             * Filter by email
             * 
             */
            $collection->addAttributeToFilter('customer_email', $email);
            
            if (1 < $collection->getSize())
            {
                $response['ecommerce']['new_customer'] = false;
                
                $customer_lifetime_value = 0;
                
                foreach ($collection as $entity)
                {
                    $customer_lifetime_value += (float) $entity->getGrandTotal();
                }
                
                $response['ecommerce']['customer_lifetime_value'] = (float) $customer_lifetime_value;
            }
            else 
            {
                $response['ecommerce']['new_customer'] = true;
                $response['ecommerce']['customer_lifetime_value'] = $order->getGrandTotal();
            }
            
            $observer->getTransport()->setResponse($response);
        }
    }
}