<?php

namespace FME\CanonicalUrl\Model\Config\Source;


class Selectstore implements \Magento\Framework\Option\ArrayInterface
{


	protected $_storeManager;
	protected $loadedData;
  
	public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
   		 )
 		  {        
  		     $this->_storeManager = $storeManager;    
  		}
    
    public function toOptionArray()
    {   
    	
 		$this->loadedData[] = [
                    'label' => __('Default Store URL'),
                'value' => '',
                ];
		
		foreach($this->_storeManager->getStores() as $store){
  

    		$this->loadedData[] = [
                    'label' => __($store->getName().'  __  '.$store->getBaseUrl()),
                    'value' => $store->getId(),
                ];
		}
 

        return $this->loadedData;
 
    }
}


     
 

