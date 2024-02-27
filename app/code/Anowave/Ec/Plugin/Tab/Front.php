<?php
/**
 * Anowave Magento 2 Price Per Customer
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
 * @package 	Anowave_Price
 * @copyright 	Copyright (c) 2021 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Ec\Plugin\Tab;

use Magento\Framework\Registry;

class Front
{
	/**
	 * @var Registry
	 */
	protected $registry;
	
	/**
	 * Construstor 
	 * 
	 * @param Registry $registry
	 */
	public function __construct
	(
		Registry $registry
	) 
	{
		$this->registry = $registry;
	}
	
	/**
	 * Get form HTML 
	 * 
	 * @param \Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tab\Front $subject
	 * @param \Closure $proceed
	 * @return unknown
	 */
	public function aroundGetFormHtml
	(
		\Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tab\Front $subject,
		\Closure $proceed
	)
	{
		$attribute = $this->registry->registry('entity_attribute');
		
		$fieldset = $subject->getForm()->getElement('front_fieldset');
		
		$events = [\Anowave\Ec\Helper\Constants::EVENT_ADD_TO_CART];
		
		$fieldset->addField
		(
			'datalayer', 'select',
			[
				'name' 		=> 'datalayer',
				'label' 	=> __('Add in dataLayer[]'),
				'title' 	=> __('Add in dataLayer[]'),
				'note' 		=> __('Add this attribute to dataLayer[] object in events such as ' . join(chr(44), $events)),
				'values' 	=> 
				[
					0 	=> __('No'),
					1 	=> __('Yes')
				],
				'value' => (int) $attribute->getData('datalayer')
			]
		);

		return $proceed();
	}
}