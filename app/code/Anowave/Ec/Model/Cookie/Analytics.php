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

namespace Anowave\Ec\Model\Cookie;

use Anowave\Ec\Model\Cookie as Cookie;

class Analytics extends Cookie
{
	/**
	 * Name of cookie that holds client id
	 */
	protected $name = '_ga';	
	
	/**
	 * Get cookie value
	 *
	 * @return string
	 */
	public function get()
	{
	    $cookie = $this->cookieManager->getCookie($this->name);
	    
	    if ($cookie && strlen($cookie) > 6)
	    {
	        return substr($cookie, 6);
	    }
	    
	    return null;
	}
}