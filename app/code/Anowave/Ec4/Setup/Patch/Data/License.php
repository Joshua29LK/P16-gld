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


declare(strict_types=1);

namespace Anowave\Ec4\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\ResourceConnection;

class License implements DataPatchInterface
{
    /** 
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    
    /**
     * @var ResourceConfig
     */
    private $resourceConfig;
    
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    
    /**
     * @var ResourceConnection
     */
    private $resource;
    
    /**
     * Constructor 
     * 
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ResourceConfig $resourceConfig
     * @param ScopeConfigInterface $scopeConfig
     * @param ResourceConnection $resource
     */
    public function __construct
    (
        ModuleDataSetupInterface $moduleDataSetup,
        ResourceConfig $resourceConfig,
        ScopeConfigInterface $scopeConfig,
        ResourceConnection $resource
    ) 
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->resourceConfig = $resourceConfig;
        $this->scopeConfig = $scopeConfig;
        $this->resource = $resource;
    }
    
    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        
        $connection = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        
        /**
         * Get table name
         * 
         * @var string $table
         */
        $table = $this->resource->getTableName('core_config_data');
        
        /**
         * Read old values
         */
        foreach 
        (
            [
                'ec4/general/license' => 'ec/ec4/license',
                'ec4/general/active'  => 'ec/ec4/active'
            ]
            as $config => $update)
        {
            $result = $connection->fetchAll("SELECT * FROM $table WHERE path = '{$config}'");
            
            if ($result)
            {
                foreach ($result as $row)
                {
                    $this->resourceConfig->saveConfig($update, $row['value'], $row['scope'], $row['scope_id']);   
                }
            }
        }

        $this->moduleDataSetup->endSetup();
    }
    
    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}