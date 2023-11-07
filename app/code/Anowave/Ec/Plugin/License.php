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

class License
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
    
    /**
     * Constructor 
     * 
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(\Magento\Framework\Module\Manager $moduleManager)
    {
        $this->moduleManager = $moduleManager;    
    }
    
    /**
     * After render 
     * 
     * @param \Anowave\Package\Block\License\Field $field
     * @param string $content
     */
    public function aroundRender(\Anowave\Package\Block\License\Field $field, \Closure $proceed, $element)
    {
        if ('ec_general_license' === $element->getId() && $this->moduleManager->isEnabled('Anowave_Ec4'))
        {
            return '';
        }
        
        return $proceed($element);
    }
}