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

namespace Anowave\Ec\Controller\Adminhtml\Log;

class Index extends \Anowave\Ec\Controller\Adminhtml\Index
{
    /**
     * Admin resource
     *
     * @var string
     */
    protected $admin_resource = 'Anowave_Ec::ec_Log';
    
    /**
     * Admin breadcrumb
     *
     * @var string
     */
    protected $admin_breadcrumb = 'Track log (GTM/GA4/FB)';
}