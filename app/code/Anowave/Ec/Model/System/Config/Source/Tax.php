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

class Tax implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * Excluding tax
	 * 
	 * @var integer
	 */
	const EXCL_TAX 	= 0;
	
	/**
	 * Including tax 
	 * 
	 * @var integer
	 */
	const INCL_TAX 	= 1;
	
	/**
	 * Excluding tax & including shipping 
	 * 
	 * @var integer
	 */
	const EXCL_TAX_INCL_SHIPPING = 2;
	
	/**
	 * Incl tax & excluding shipping 
	 * 
	 * @var integer
	 */
	const INCL_TAX_EXCL_SHIPPING = 3;
	
	/**
	 * @return []
	 */
	public function toOptionArray()
	{
		return 
		[
			[
				'value' => self::EXCL_TAX, 
				'label' => __('Excluding both Tax & Shipping')
			],
			[
				'value' => self::INCL_TAX, 
				'label' => __('Including both Tax & Shipping')
			],
		    [
		        'value' => self::INCL_TAX_EXCL_SHIPPING,
		        'label' => __('Including Tax but Excluding shipping')
		    ],
		    [
		        'value' => self::EXCL_TAX_INCL_SHIPPING,
		        'label' => __('Excluding Tax but Including shipping')
		    ]
		];
	}
}