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

namespace Anowave\Ec\Plugin\Order;

class PlaceAfter
{
    /**
     * @var \Anowave\Ec\Model\TransactionFactory
     */
    protected $transactionFactory;
    
    /**
     * @var \Anowave\Ec\Model\Api\Measurement\Protocol
     */
    protected $protocol;
    
    /**
     * @var \Anowave\Ec\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Anowave\Ec\Logger\Logger
     */
    protected $logger;
    
    /**
     * Constructor
     *
     * @param \Anowave\Ec\Model\TransactionFactory $transactionFactory
     */
    public function __construct
    (
        \Anowave\Ec\Model\TransactionFactory $transactionFactory,
        \Anowave\Ec\Model\Api\Measurement\Protocol $protocol,
        \Anowave\Ec\Helper\Data $helper,
        \Anowave\Ec\Logger\Logger $logger
    )
    {
        /**
         * Set transaction factory 
         * 
         * @var \Anowave\Ec\Model\TransactionFactory $transactionFactory
         */
        $this->transactionFactory = $transactionFactory;
        
        /**
         * Set protocol
         *
         * @var \Anowave\Ec\Model\Api\Measurement\Protocol $protocol
         */
        $this->protocol = $protocol;
        
        /**
         * Set helper
         *
         * @var \Anowave\Ec\Observer\Order\Cancel\After $helper
         */
        $this->helper = $helper;
        
        /**
         * Set logger 
         * 
         * @var \Anowave\Ec\Plugin\Order\PlaceAfter $logger
         */
        $this->logger = $logger;
    }

    
    /**
     * @param \Magento\Sales\Api\OrderManagementInterface $orderManagementInterface
     * @param \Magento\Sales\Model\Order\Interceptor $order
     * @return $order
     */
    public function afterPlace(\Magento\Sales\Api\OrderManagementInterface $orderManagementInterface , $order)
    {
        $transaction = $this->transactionFactory->create();
        
        $transaction->setData
        (
            [
                'ec_track'    => \Anowave\Ec\Helper\Constants::FLAG_PLACED,
                'ec_order_id' => (int) $order->getId()
            ]
        );
        
        if (isset($_COOKIE['_ga']))
        {
            $transaction->setEcCookieGa($_COOKIE['_ga']);
        }
        
        if (!(php_sapi_name() == 'cli')) 
        {
            if (isset($_SERVER['HTTP_USER_AGENT']))
            {
                $transaction->setEcUserAgent($_SERVER['HTTP_USER_AGENT']);
            }
        }
         
        $transaction->save();
        
        /**
         * Track order on placed
         */
        if ($this->helper->useMeasurementProtocolOnlyPlaced())
        {
            if (false !== $this->protocol->purchaseById($order->getId()))
            {
                $this->logger->info("ORDER ID ({$order->getIncrementId()}) tracked to GA4 successfully.");
            }
            else 
            {
                $this->logger->info("Failed to track {$order->getId()} to GA4.");
            }
        }

       return $order;
    }
}