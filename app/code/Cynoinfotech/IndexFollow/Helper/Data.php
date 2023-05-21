<?php
/**
 * @author CynoInfotech Team
 * @package Cynoinfotech_IndexFollow
 */
namespace Cynoinfotech\IndexFollow\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
                
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Serialize\SerializerInterface $serializerInterface,
        \Magento\Framework\UrlInterface $urlInterface
    ) {
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_serializerInterface = $serializerInterface;
        $this->_urlInterface = $urlInterface;       
        parent::__construct($context);
    }
    
        /**
         * Functionality to get configuration values of plugin
         * @param $configPath: System xml config path
         * @return value of requested configuration
         */
     
        public function getConfig($configPath)
        {
            return $this->_scopeConfig->getValue(
                $configPath,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }    
        
        /*-----------------------------Loader Image------------------------*/        
        /**
         * getLoaderMethod     
         */
        public function isSetForCustomUrl()
        {
            $customUrlData = $this->getConfig('cynoinfotech_indexfollow/iffcu/custom_url');
            $customUrls = $this->_serializerInterface->unserialize($customUrlData);
            
            if (is_array($customUrls)) {
                 foreach ($customUrls as $customUrlData) {
                    if ($customUrlData['indexfollow_enable']) {
                        /* ------currentUrl-------*/                    
                        $currentUrl=$this->_urlInterface->getCurrentUrl();
                        /* ------customUrl-------*/
                        $customUrl=$customUrlData['url'];
                        
                        if ($currentUrl == $customUrl) {    
                            $indexValue= $customUrlData['index_value'];
                            $followValue= $customUrlData['follow_value'];                            
                            return $robotsValue = $this->getRobotsValue($indexValue, $followValue);    
                        }                    
                    }                 
                 }            
            }
            
            return ;
            
        }
        
        public function getRobotsValue($indexValue = 1, $followValue = 1) 
        {
        
            $data='';            
                if ($indexValue==1) {
                    $data .= 'INDEX';
                } else if ($indexValue==2) {
                    $data .= 'NOINDEX';
                }
                
                $data .= ',';                
                if ($followValue==1) {
                    $data .= 'FOLLOW';
                } else if ($followValue==2) {
                    $data .= 'NOFOLLOW';
                }
                
            return $data;    
        
        }   
    
}
