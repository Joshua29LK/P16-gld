<?php
namespace FME\CanonicalUrl\Helper;

use Magento\Store\Model\Store;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const XML_CANONICALURL_ENABLE               ='canonicalurl/general/canonicalurl_mod_enable';
    const XML_CANONICALURL_PAGINATIONPAGES      ='canonicalurl/category_canonical_tags/paginationpages_mod_enable';
    const XML_PAGINATIONPAGES_CANONICAL         ='canonicalurl/category_canonical_tags/paginationpages_canonical_enable';
    const XML_LAYERED_CANONICAL                 ='canonicalurl/category_canonical_tags/canonicalurl_layered_enable';
    const XML_EXCLUDE_CANONICAL                 ='canonicalurl/general/exclude_canonical_mod';
    const XML_TRAILINGSLASH_CANONICAL           ='canonicalurl/general/canonicalurl_trailingslash_enable';
    const XML_STORE_CANONICAL           ='canonicalurl/general/canonicalurl_storeurl_enable';
    const XML_CUSTOMURL_CANONICAL           ='canonicalurl/general/canonicalurl_customurl_enable';
    const XML_CATEGORY_CANONICAL_ENABLE           ='canonicalurl/category_canonical_tags/category_canonical_tags_enable';    
    const XML_PRODUCT_CANONICAL_ENABLE           ='canonicalurl/product_canonical_tags/product_canonical_tags_enable';    
    const XML_INCLUDE_CATEGORY_PATH           ='canonicalurl/product_canonical_tags/product_canonical_tags_include_cat_path';    
    const XML_CATEGORY_PATH_STRUCTURE           ='canonicalurl/product_canonical_tags/product_canonical_tags_cat_path_structure';    
     const XML_CMS_CANONICAL_ENABLE           ='canonicalurl/cms_canonical_tags/cms_canonical_tags_enable';    
    
       
      public function isEnabledInFrontend()
    {
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_CANONICALURL_ENABLE, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
    }
    
      public function isCategoryEnabled()
    {
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_CATEGORY_CANONICAL_ENABLE, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
    }

      public function isProductEnabled()
    {
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_PRODUCT_CANONICAL_ENABLE, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
    }

     public function isCmsEnabled()
    {
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_CMS_CANONICAL_ENABLE, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
    }

      public function isCategoryPathEnabled()
    {
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_INCLUDE_CATEGORY_PATH, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
    }

      public function isCategoryPathStructure()
    { 
        $enabled = $this->scopeConfig->getValue(self::XML_CATEGORY_PATH_STRUCTURE, ScopeInterface::SCOPE_STORE);
        return $enabled;
    }

      public function getPaginationPagesCanonical()
    {
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_PAGINATIONPAGES_CANONICAL, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
    }
    
     
 
     public function getCanonicalPaginationPages()
    {
         $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_CANONICALURL_PAGINATIONPAGES, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;

      
    }

    public function getCanonicalLayeredNav()
    {
        $isEnabled = null;
        $enabled = $this->scopeConfig->getValue(self::XML_LAYERED_CANONICAL, ScopeInterface::SCOPE_STORE);

        if ($enabled == null || $enabled == 'no') {
            $isEnabled = 'no';
        
        return $isEnabled;
        
        }

        if ($enabled == 'filteredpage') {
            $isEnabled = 'filteredpage';
        
        return $isEnabled;
        
        }

        if ($enabled == 'currentcat') {
            $isEnabled = 'currentcat';
        
        return $isEnabled;
        
        }
    }


    public function getExcludePages()
    {
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_EXCLUDE_CANONICAL, ScopeInterface::SCOPE_STORE);

        if ($enabled == null) {
            $isEnabled = false;
        }
        else{
            $isEnabled = $enabled;
        }
        
        return $isEnabled;
    }

    public function gettrailingslash()
    {
        $isEnabled = null;
        $enabled = $this->scopeConfig->getValue(self::XML_TRAILINGSLASH_CANONICAL, ScopeInterface::SCOPE_STORE);
      

        if ($enabled == 'add') {
            $isEnabled = 'add';
        
        return $isEnabled;
        
        }

        if ($enabled == 'crop') {
            $isEnabled = 'crop';
        
        return $isEnabled;
        
        }
    }

    public function getstorecanonical(){


        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_STORE_CANONICAL, ScopeInterface::SCOPE_STORE);

        if ($enabled == null) {
            $isEnabled = false;
        }
        else{
            $isEnabled = $enabled;
        }
        
        return $isEnabled;

    }


    public function getcustomurlcanonical(){


        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_CUSTOMURL_CANONICAL, ScopeInterface::SCOPE_STORE);

        if ($enabled == null) {
            $isEnabled = false;
        }
        else{
            $isEnabled = $enabled;
        }
        
        return $isEnabled;

    }


  
}