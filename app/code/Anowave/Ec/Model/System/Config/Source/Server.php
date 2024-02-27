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

namespace Anowave\Ec\Model\System\Config\Source;

class Server implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Disable server-side tracking
     * @var integer
     */
    const TRACK_SERVER_SIDE_DISABLED = 0;
    
    /**
     * Enable server-side tracking on success page
     * @var integer
     */
    const TRACK_SERVER_SIDE_ENABLED_ON_SUCCESS = 1;
    
    /**
     * Enable server-side tracking on placed order
     * @var integer
     */
    const TRACK_SERVER_SIDE_ENABLED_ON_PLACED = 2;
    
	/**
	 * @return []
	 */
	public function toOptionArray()
	{
		return 
		[
		    [
		        'value' => static::TRACK_SERVER_SIDE_DISABLED,
		        'label' => __('No')
		    ],
			[
				'value' => static::TRACK_SERVER_SIDE_ENABLED_ON_SUCCESS,
				'label' => __('Yes, on success page')
			],
		    [
		        'value' => static::TRACK_SERVER_SIDE_ENABLED_ON_PLACED,
		        'label' => __('Yes, on placed order')
		    ]
		];
	}
}