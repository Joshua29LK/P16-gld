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
 * @copyright 	Copyright (c) 2022 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Ec4\Helper;

class Data extends \Anowave\Package\Helper\Package
{
    /**
     * Shared measurement ID config path
     *
     * @var string
     */
    const CONFIG_SHARED = 'ec/api/google_gtm_ua4_measurement_id';
    
    /**
     * Dedicated measurement ID config path
     *
     * @var string
     */
    const CONFIG_DEDICATED = 'ec/api/google_gtm_ua4_measurement_id_backend';
    
    /**
     * Package name
     * @var string
     */
    protected $package = 'MAGE2-GTMGA4';
    
    /**
     * Config path
     * @var string
     */
    protected $config = 'ec4/general/license';
    
    /**
     * @var \Anowave\Ec\Helper\Data
     */
    protected $baseHelper;
    
    /**
     * Constructor 
     * 
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Anowave\Ec\Helper\Data $baseHelper
     */
    public function __construct
    (
        \Magento\Framework\App\Helper\Context $context,
        \Anowave\Ec\Helper\Data $baseHelper
    )
    {
        $this->baseHelper = $baseHelper;
        
        parent::__construct($context);
    }
    
    /**
     * Check if GA4 is enabled
     *
     * @return bool
     */
    public function isGA4Enabled() : bool
    {
        return 1 === $this->filter((int) $this->getConfig('ec4/general/active'));
    }
    
    /**
     * Get base helper
     * 
     * @return \Anowave\Ec\Helper\Data
     */
    public function getBaseHelper()
    {
        return $this->baseHelper;
    }
    
    
    /**
     * Get Measurement ID
     *
     * @param int $store
     * @return string
     */
    public function getMeasurementId(int $store = 0) : string
    {
        return (string) $this->getConfig($this->getMeasurementIdConfig());
    }
    
    /**
     * Get measurement api secret
     *
     * @return string
     */
    public function getMeasurementApiSecret() : string
    {
        return (string) $this->getConfig('ec/api/google_gtm_ua4_measurement_api_secret');
    }
    
    /**
     * Get measurement id config string
     *
     * @return string
     */
    public function getMeasurementIdConfig() : string
    {
        return \Anowave\Ec4\Model\System\Config\Source\Type::TYPE_DEDICATED === (int) $this->getConfig('ec/api/measurement_id_type') ? static::CONFIG_DEDICATED : static::CONFIG_SHARED;
    }
}