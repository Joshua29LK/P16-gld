<?php
/**
 * @author CynoInfotech Team
 * @package Cynoinfotech_IndexFollow
 */
namespace Cynoinfotech\IndexFollow\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;

class Robots implements ObserverInterface
{

        protected $_request;

        protected $_layoutFactory;

        public function __construct(
            \Magento\Framework\App\Request\Http $request,
            \Magento\Framework\UrlInterface $urlInterface,
            \Magento\Framework\Registry $registry,
            \Magento\Cms\Model\Page $cmsPage,
            \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
            \Cynoinfotech\IndexFollow\Helper\Data $helper,            
            \Magento\Framework\View\Page\Config $layoutFactory
        ) {
           $this->_request = $request;
           $this->_urlInterface = $urlInterface;
           $this->_registry = $registry;
           $this->_cmsPage = $cmsPage;
           $this->_categoryCollectionFactory = $categoryCollectionFactory;
           $this->_helper = $helper;
           $this->_layoutFactory = $layoutFactory;
        }

           public function execute(Observer $observer) 
           {          
           
           if (!$this->_helper->getConfig('cynoinfotech_indexfollow/general/enable')) {
                return ;
           }
          
           $fullAction = $this->_request->getFullActionName();
                      
           if (($fullAction == 'catalog_product_view') AND ($this->_helper->getConfig('cynoinfotech_indexfollow/efcpcp/product_enable'))) {           
                $product = $this->_registry->registry('current_product');
                
                if ((!empty($product->getIndexfollowEnable())) AND ($product->getIndexfollowEnable())) {
                        $indexValue = $product->getIndexfollowIndexvalue();
                        $followValue = $product->getIndexfollowFollowvalue();                        
                        $robotsValue = $this->_helper->getRobotsValue($indexValue, $followValue);
                        $this->_layoutFactory->setRobots($robotsValue);                        
                } else {
                    $categoryIds = $product->getCategoryIds();
                                    
                    $collection = $this->_categoryCollectionFactory->create();
                    $collection->addAttributeToSelect('*');
                    $collection->addAttributeToFilter('entity_id', $categoryIds);
                    $collection->addOrderField('indexfollow_priority');            
                        foreach ($collection as $category) {
                            if ((!empty($category->getIndexfollowEnable())) AND ($category->getIndexfollowEnable()) AND (!empty($category->getIndexfollowAssociatedproducts())) AND($category->getIndexfollowAssociatedproducts()) ) {
                                $indexValue = $category->getIndexfollowIndexvalue();
                                $followValue = $category->getIndexfollowFollowvalue();
                                $robotsValue = $this->_helper->getRobotsValue($indexValue, $followValue);
                                $this->_layoutFactory->setRobots($robotsValue);                                
                                break;
                            }
                        }                    
                }
           } else if ($fullAction == 'catalog_category_view') {
                $category = $this->_registry->registry('current_category');
                
                if ((!empty($category->getIndexfollowEnable())) AND ($category->getIndexfollowEnable())) {
                        $indexValue = $category->getIndexfollowIndexvalue();
                        $followValue = $category->getIndexfollowFollowvalue();
                        $robotsValue = $this->_helper->getRobotsValue($indexValue, $followValue);
                        $this->_layoutFactory->setRobots($robotsValue);
                }  
           } else if ($fullAction == 'cms_index_index' || $fullAction == 'cms_page_view') {
                $cmsPage = $this->_cmsPage;
            
                if ((!empty($cmsPage->getIndexfollowEnable())) AND ($cmsPage->getIndexfollowEnable())) {
                        $indexValue = $cmsPage->getIndexfollowIndexvalue();
                        $followValue = $cmsPage->getIndexfollowFollowvalue();                        
                        $robotsValue = $this->_helper->getRobotsValue($indexValue, $followValue);
                        $this->_layoutFactory->setRobots($robotsValue);                                
                }       
           } else {       
                $robotsValue =$this->_helper->isSetForCustomUrl();
                if (!empty($robotsValue)) {
                    $this->_layoutFactory->setRobots($robotsValue);            
                }
           }       
       
           }
        
}