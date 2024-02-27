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

namespace Anowave\Ec\Model\System\Config\Source\Consent;

class Widget implements \Magento\Framework\Option\ArrayInterface
{
    const KEEP_HIDE = 0;
    const KEEP_BOTTOM_LEFT = 1;
    const KEEP_BOTTOM_RIGHT = 2;
    const KEEP_BOTTOM_MIDDLE = 3;
    
	/**
	 * @return []
	 */
	public function toOptionArray()
	{
		return 
		[
			[
				'value' => static::KEEP_HIDE, 
				'label' => __('Hide consent widget on accept/decline')
			],
			[
				'value' => static::KEEP_BOTTOM_LEFT, 
				'label' => __('Keep sticky consent widget on accept/decline (bottom left)')
			]
		];
	}
}