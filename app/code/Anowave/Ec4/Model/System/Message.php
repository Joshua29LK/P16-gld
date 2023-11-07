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

namespace Anowave\Ec4\Model\System;

use Magento\Framework\Notification\MessageInterface;

class Message implements MessageInterface
{
    /**
     * Message identity
     */
    const MESSAGE_IDENTITY = 'ec4_system_notification';
    
    /**
     * @var \Anowave\Ec4\Helper\Data
     */
    protected $helper;
    
    /**
     * Constructor 
     * 
     * @param \Anowave\Ec4\Helper\Data $helper
     */
    public function __construct(\Anowave\Ec4\Helper\Data $helper)
    {
        $this->helper = $helper;
    }
    
    /**
     * Retrieve unique system message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return static::MESSAGE_IDENTITY;
    }
    
    /**
     * Check whether the system message should be shown
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return !$this->helper->isGA4Enabled();
    }
    
    /**
     * Retrieve system message text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getText()
    {
        if (!$this->helper->isGA4Enabled())
        {
            return __('Google Analytics 4 tracking is not enabled. Enter valid license key in Stores -> Configuration -> Anowave -> Google Tag Manager GA4 -> Google Analytics 4 -> License');
        }
            
        return __('Google Analytics 4 is enabled and activated.');
    }
    
    /**
     * Retrieve system message severity
     * Possible default system message types:
     * - MessageInterface::SEVERITY_CRITICAL
     * - MessageInterface::SEVERITY_MAJOR
     * - MessageInterface::SEVERITY_MINOR
     * - MessageInterface::SEVERITY_NOTICE
     *
     * @return int
     */
    public function getSeverity()
    {
        return static::SEVERITY_CRITICAL;
    }
}