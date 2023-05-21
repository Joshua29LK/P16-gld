<?php
namespace FME\CanonicalUrl\Observer\Product;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Magento\Cms\Model\PageFactory;

class CanonicalUrl implements ObserverInterface{

	public $toolbarBlock = null;
	public $pagerBlock = null;
	private $pageConfig;
	public $productListBlock;
	private $urlBuilder;
	protected $helper;
	protected $_storeManager;
	protected $_categoryFactory;
	protected $_url;
	protected $request;
	protected $_registry;
	protected $_productloader; 
	protected $categoryRepository;
	protected $categoryHelper;
	protected $_cmsPage;
	protected $pageFactory;


	public function __construct(
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\Registry $registry,
		\Magento\Catalog\Block\Product\ProductList\Toolbar $toolbarBlock,
		\Magento\Theme\Block\Html\Pager $pagerBlock,
		\Magento\Framework\View\Page\Config $pageConfig,
		\Magento\Catalog\Block\Product\ListProduct $productListBlock,
		UrlInterface $urlBuilder,
		\FME\CanonicalUrl\Helper\Data $helper,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Framework\Url $url,
		\Magento\Catalog\Model\ProductFactory $_productloader,
		\Magento\Catalog\Model\CategoryRepository $categoryRepository,
		\Magento\Catalog\Helper\Category $categoryHelper,
		\Magento\Cms\Model\Page $cmsPage,
		PageFactory $pageFactory
	) {
		$this->request = $request;
		$this->toolbarBlock = $toolbarBlock;
		$this->pagerBlock = $pagerBlock;
		$this->pageConfig = $pageConfig;
		$this->urlBuilder = $urlBuilder;
		$this->helper = $helper;
		$this->_storeManager = $storeManager;
		$this->_categoryFactory = $categoryFactory;
		$this->_url = $url;
		$this->_registry = $registry;
		$this->_productloader = $_productloader;
		$this->categoryHelper = $categoryHelper;
		$this->categoryRepository = $categoryRepository;
		$this->_cmsPage = $cmsPage;
		$this->pageFactory = $pageFactory;
	}
	/**

	 * Below is the method that will fire whenever the event runs!

	 *

	 * @param Observer $observer

	 */

	public function execute(\Magento\Framework\Event\Observer $observer) {

		$currentUrl = $this->_url->getCurrentUrl();

		$url = $this->_url->getRebuiltUrl($currentUrl);

		$url = str_replace("/index.php", "", $url);

		$url_data = parse_url($url);

		if (isset($url_data['query'])) {
			parse_str(html_entity_decode($url_data['query']), $getparams);
		}
		else{

			$getparams = [];
		}

		$fullactionname = $this->request->getFullActionName();

		$pageparams = $this->request->getParams();

		$layer = $this->helper->getCanonicalLayeredNav();

		$exclude = '';

		if ($this->helper->getExcludePages()) {

			$exclude = preg_split('/\s+/', $this->helper->getExcludePages());

		}
 

		if ($this->helper->isEnabledInFrontend()) {
 

			if($fullactionname == 'catalog_category_view'){

				$productListBlock = $observer->getEvent()->getLayout()->getBlock('category.products.list');
				$category = $productListBlock->getLayer()->getCurrentCategory();

				$this->pageConfig->getAssetCollection()->remove($category->getUrl());

				$toolbarBlock = $productListBlock->getToolbarBlock();

				$pagerBlock = $toolbarBlock->getChildBlock('product_list_toolbar_pager');

				$pagerBlock->setLimit($toolbarBlock->getLimit())
				->setCollection($productListBlock->getLayer()->getProductCollection());

				$collection = $pagerBlock->getCollection();

				$this->getCanonicalForCategories($fullactionname, $exclude, $pageparams, $pagerBlock, $getparams, $layer);

			}

			else if($fullactionname == 'catalog_product_view'){

				$this->getCanonicalForProducts($fullactionname, $exclude);
 
			}

			else if($fullactionname == 'cms_page_view'){


				if ($this->helper->isCmsEnabled()) {

					$this->getCmsPagesCanonical($fullactionname, $exclude);
 					
				}
  
			}

			else if ($fullactionname == 'catalogsearch_result_index') {

				if (!($this->helper->getExcludePages() && in_array($fullactionname, $exclude))) {


					$currentUrl = $this->_url->getCurrentUrl();

					$url = $this->_url->getRebuiltUrl($currentUrl);

					$url = str_replace("/index.php", "", $url);

					$url_data = parse_url($url);

					if (isset($url_data['query'])) {
						$url = str_replace('?' . $url_data['query'], '', $url);
					}

					$this->getTrailingSlashForCanonicalUrl($url);
				}
			}

			else{

				if (!($this->helper->getExcludePages() && in_array($fullactionname, $exclude))) {

					$currentUrl = $this->_url->getCurrentUrl();

					$url = $this->_url->getRebuiltUrl($currentUrl);

					$url = str_replace("/index.php", "", $url);

					$this->getTrailingSlashForCanonicalUrl($url);
				}
			}

		}


	}

	public function getCanonicalForCategories($fullactionname, $exclude, $pageparams, $pagerBlock, $getparams, $layer){
 
		if ($this->helper->isCategoryEnabled()) {

			if (!($this->helper->getExcludePages() && in_array($fullactionname, $exclude))) {

				if ($this->helper->getPaginationPagesCanonical()) {

					if (isset($pageparams[$pagerBlock->getPageVarName()])) {

						$this->getCanonicalUrlForPaginationPages($pagerBlock->getPageVarName(),$getparams);

					}


				}

				if ($layer == 'currentcat') {

					$this->getCanonicalUrlForCurrentCat($pagerBlock->getPageVarName(),$getparams);


				}

				if ($layer == 'filteredpage') {

					$this->getCanonicalUrlForLayeredNav($pagerBlock->getPageVarName(),$getparams);


				}

				if ($layer == 'no') {

					if (isset($pagerBlock)) {
						$pagevar = $pagerBlock->getPageVarName();

					} else {
						$pagevar = '';
					}

					$this->getCanonicalUrlForCatalog($pagevar,$getparams);

				}

			}

			if ($this->helper->getCanonicalPaginationPages()) {


				$has_prev_page = $has_next_page = false;
				$next_page_url = $prev_page_url = '';

				$current_page = $pagerBlock->getCurrentPage();
				$last_page = $pagerBlock->getLastPageNum();

				if ($current_page > $last_page) {

					$current_page = $last_page;
				}

				if ($current_page != 1 && $last_page != 1) {
					$has_prev_page = true;
				}

				if ($has_prev_page) {

					$this->pageConfig->addRemotePageAsset(html_entity_decode(
						$this->getPageUrl($pagerBlock->getCollection()->getCurPage(-1)
							, $pagerBlock->getPageVarName(),$getparams)),
					'link_rel',
					['attributes' => ['rel' => 'prev']]
				);


				}

				if ($current_page != $last_page && $last_page != 0) {
					$has_next_page = true;

				}

				if ($has_next_page) {

					$this->pageConfig->addRemotePageAsset(html_entity_decode(
						$this->getPageUrl($pagerBlock->getCollection()->getCurPage(+1)
							, $pagerBlock->getPageVarName(),$getparams)),
					'link_rel',
					['attributes' => ['rel' => 'next']]
				);



				}


			}	

		}

	}

	public function getCanonicalForProducts($fullactionname, $exclude){

		if ($this->helper->isProductEnabled()) {

			if (!($this->helper->getExcludePages() && in_array($fullactionname, $exclude))) {


				$productId = $this->_registry->registry('current_product')->getId();
				$product = $this->_productloader->create()->load($productId);
				$overrideCanonical = $product->getOverrideCanonicalUrl();

				$categoryPathStr = $this->helper->isCategoryPathStructure();

				$categoryId = $product->getCategoryId();

				if(!$categoryId){
					$categoryId = $product->getCategoryIds();
					$categoryId = $categoryId[0];
				}

				$categoryObj = $this->categoryRepository->get($categoryId);

				if(empty($overrideCanonical)){
					
					if ($this->helper->isCategoryPathEnabled()) {

						if( empty($product->getCanonicalPrimaryCategoryUrl()) || $product->getCanonicalPrimaryCategoryUrl() == 'config' ){
 

							if($categoryPathStr == 1){

								if($categoryObj->getParentCategories()){
									$categoriesArr = array();
									foreach ($categoryObj->getParentCategories() as $parent) {

										$categoriesArr[$parent->getId()]['id'] = $parent->getId();
										$categoriesArr[$parent->getId()]['name'] = $parent->getName();
										$categoriesArr[$parent->getId()]['level'] = $parent->getLevel();
										$categoriesArr[$parent->getId()]['urlkey'] = $parent->getUrlKey();

									}

									array_multisort(array_column($categoriesArr, 'level'), SORT_ASC, $categoriesArr);
									$currentUrl = $this->_url->getCurrentUrl();
									$url = $this->_url->getRebuiltUrl($currentUrl);
									$url = str_replace("/index.php", "", $url);
									if(!empty($categoriesArr[0]['urlkey'])){
										$url = str_replace($product->getUrlKey(),$categoriesArr[0]['urlkey'].'/'.$product->getUrlKey(),$url);
									}
									 
									$url = $this->getStoreCanonicalUrl($url);
									$this->getTrailingSlashForCanonicalUrl($url); 

								}else{

									$categoryUrlKey = $categoryObj->getUrlKey();
									$currentUrl = $this->_url->getCurrentUrl();
									$url = $this->_url->getRebuiltUrl($currentUrl);
									$url = str_replace("/index.php", "", $url);
									if(!empty($categoryUrlKey)){
										$url = str_replace($product->getUrlKey(),$categoryUrlKey.'/'.$product->getUrlKey(),$url);
									}
									 
									$url = $this->getStoreCanonicalUrl($url);
									$this->getTrailingSlashForCanonicalUrl($url);

								}

							}

							if($categoryPathStr == 2){
 
								if($categoryObj->getParentCategories()){
									$categoriesArr = array();
									foreach ($categoryObj->getParentCategories() as $parent) {

										$categoriesArr[$parent->getId()]['id'] = $parent->getId();
										$categoriesArr[$parent->getId()]['name'] = $parent->getName();
										$categoriesArr[$parent->getId()]['level'] = $parent->getLevel();
										$categoriesArr[$parent->getId()]['urlkey'] = $parent->getUrlKey();

									}

									array_multisort(array_column($categoriesArr, 'level'), SORT_ASC, $categoriesArr);
									$categoryFullPath = implode('/', array_column($categoriesArr, 'urlkey'));
									$currentUrl = $this->_url->getCurrentUrl();
									$url = $this->_url->getRebuiltUrl($currentUrl);
									$url = str_replace("/index.php", "", $url);
									if(!empty($categoryFullPath)){
										$url = str_replace($product->getUrlKey(),$categoryFullPath.'/'.$product->getUrlKey(),$url);
									}
									 
									$url = $this->getStoreCanonicalUrl($url);
									$this->getTrailingSlashForCanonicalUrl($url); 

								}else{

									$categoryUrlKey = $categoryObj->getUrlKey();
									$currentUrl = $this->_url->getCurrentUrl();
									$url = $this->_url->getRebuiltUrl($currentUrl);
									$url = str_replace("/index.php", "", $url);
									if(!empty($categoryUrlKey)){
										$url = str_replace($product->getUrlKey(),$categoryUrlKey.'/'.$product->getUrlKey(),$url);
									}
									$url = $this->getStoreCanonicalUrl($url);
									$this->getTrailingSlashForCanonicalUrl($url);

								}
								
							}

						}else{


							$categoryId = $product->getCanonicalPrimaryCategoryUrl();
							$categoryObj = $this->categoryRepository->get($categoryId);

							if($categoryObj->getParentCategories()){
								$categoriesArr = array();
								foreach ($categoryObj->getParentCategories() as $parent) {

									$categoriesArr[$parent->getId()]['id'] = $parent->getId();
									$categoriesArr[$parent->getId()]['name'] = $parent->getName();
									$categoriesArr[$parent->getId()]['level'] = $parent->getLevel();
									$categoriesArr[$parent->getId()]['urlkey'] = $parent->getUrlKey();

								}

								array_multisort(array_column($categoriesArr, 'level'), SORT_ASC, $categoriesArr);
								$categoryFullPath = implode('/', array_column($categoriesArr, 'urlkey'));
								$currentUrl = $this->_url->getCurrentUrl();
								$url = $this->_url->getRebuiltUrl($currentUrl);
								$url = str_replace("/index.php", "", $url);
								if(!empty($categoryFullPath)){
									$url = str_replace($product->getUrlKey(),$categoryFullPath.'/'.$product->getUrlKey(),$url);
								}
								$url = $this->getStoreCanonicalUrl($url);
								$this->getTrailingSlashForCanonicalUrl($url); 

							}else{

								$categoryUrlKey = $categoryObj->getUrlKey();
								$currentUrl = $this->_url->getCurrentUrl();
								$url = $this->_url->getRebuiltUrl($currentUrl);
								$url = str_replace("/index.php", "", $url);
								if(!empty($categoryUrlKey)){
									$url = str_replace($product->getUrlKey(),$categoryUrlKey.'/'.$product->getUrlKey(),$url);
								}
								$url = $this->getStoreCanonicalUrl($url);
								$this->getTrailingSlashForCanonicalUrl($url);

							}


						}

					}else{

							$currentUrl = $this->_url->getCurrentUrl();
							$url = $this->_url->getRebuiltUrl($currentUrl);
							$url = str_replace("/index.php", "", $url);
				
							$url = $this->getStoreCanonicalUrl($url);
							$this->getTrailingSlashForCanonicalUrl($url); 
					}

				}else{

					$currentUrl = $this->_url->getCurrentUrl();
					$url = $this->_url->getRebuiltUrl($currentUrl);
					$url = str_replace("/index.php", "", $url);
					$urlparse = parse_url($url);
					$url_path = $urlparse['path'];
					$url = str_replace($url_path,'/'.$overrideCanonical,$url);
					$url = $this->getStoreCanonicalUrl($url);
					$this->getTrailingSlashForCanonicalUrl($url); 
				}
			}
		}

	}
	
	public function getCmsPagesCanonical($fullactionname, $exclude){

		if (!($this->helper->getExcludePages() && in_array($fullactionname, $exclude))) {

			$cmsId = $this->_cmsPage->getId();

			if($cmsId){

				$page = $this->pageFactory->create()->load($cmsId);	

				$currentUrl = $this->_url->getCurrentUrl();
				$url = $this->_url->getRebuiltUrl($currentUrl);
				$url = str_replace("/index.php", "", $url);
				
				if($page->getOverrideCanonicalUrl())
				{

					$urlparse = parse_url($url);
					$url_path = $urlparse['path'];
					if(isset($urlparse['query'])){
						$url_query = $urlparse['query'];
						$url = str_replace($url_query,'',$url);
						$url = str_replace('?','',$url);
					
					}
					$url = str_replace($url_path,'/'.$page->getOverrideCanonicalUrl(),$url);

				}

				$this->getTrailingSlashForCanonicalUrl($url); 
		 
			}
		}

	}

	protected function getPageUrl($pagenum, $pagevar ,$getparams) {

		$urlParams = [];
		$urlParams['_current'] = false;
		$urlParams['_escape'] = true;
		$urlParams['_use_rewrite'] = true;
		$getparams[$pagevar] = $pagenum;
		$urlParams['_query'] = $getparams;
		return html_entity_decode($this->urlBuilder->getUrl('*/*/*', $urlParams));

	}

	public function getCanonicalUrlForPaginationPages($pagevar,$getparams) {

		$categoryId = $this->_registry->registry('current_category')->getId();
		$categoryObj = $this->categoryRepository->get($categoryId);

		$currentUrl = $this->_url->getCurrentUrl();
		$url = $this->_url->getRebuiltUrl($currentUrl);
		$url = str_replace("/index.php", "", $url);
		$url_data = parse_url($url);
 
		if (sizeof($getparams) == 1 && array_key_exists($pagevar, $getparams)) {

			$currpage = $getparams[$pagevar];

			if ($currpage == 1) {
 				
				if (isset($url_data['query'])) {
					$url = str_replace('?' . $url_data['query'], '', $url);
				}
  	 
			} else {
 
				if(isset($url_data['query'])){
					$url_query = $url_data['query'];
					$url = str_replace($url_query,'',$url);
					$url = str_replace('?','',$url);
				}
   
			}

			$overrideCanonical = $categoryObj->getOverrideCanonicalUrl();
			$url = $this->generateOverrideCanonicalUrl($overrideCanonical, $url);
 
			$url = $this->getStoreCanonicalUrl($url);
			$this->getTrailingSlashForCanonicalUrl($url);

		}

	}

	public function getCanonicalUrlForCurrentCat($pagevar,$getparams) {

		$categoryId = $this->_registry->registry('current_category')->getId();
		$categoryObj = $this->categoryRepository->get($categoryId);

		$overrideCanonical = $categoryObj->getOverrideCanonicalUrl();

		if ($this->helper->getPaginationPagesCanonical()) {

			if (!(sizeof($getparams) == 1 && array_key_exists($pagevar, $getparams))) {

				 
				$url = $this->generateCanonicalForCurrCat($overrideCanonical);

				$url = $this->getStoreCanonicalUrl($url);
				$this->getTrailingSlashForCanonicalUrl($url);
  
			}
		}

		if (!($this->helper->getPaginationPagesCanonical())) {

			if (sizeof($getparams) == 1 && array_key_exists($pagevar, $getparams)){
				 
				$url = $this->generateCanonicalForCurrCat($overrideCanonical);

				$url = $this->getStoreCanonicalUrl($url);
					
			}else{
				  
				$url = $this->generateCanonicalForCurrCat($overrideCanonical);

				$url = $this->getStoreCanonicalUrl($url);
				$this->getTrailingSlashForCanonicalUrl($url);

			}
 
		}


	}

	public function generateCanonicalForCurrCat($overrideCanonical){

		$currentUrl = $this->_url->getCurrentUrl();

		$url = $this->_url->getRebuiltUrl($currentUrl);

		$url = str_replace("/index.php", "", $url);

		$url_data = parse_url($url);

		if (isset($url_data['query'])) {
			$url = str_replace('?' . $url_data['query'], '', $url);
		}

		if($overrideCanonical){
				
			$urlparse = parse_url($url);
			$url_path = $urlparse['path'];

			if(isset($urlparse['query'])){
				$url_query = $urlparse['query'];
				$url = str_replace($url_query,'',$url);
				$url = str_replace('?','',$url);
			}

			$url = str_replace($url_path,'/'.$overrideCanonical,$url);
		}

		return $url;

	}


	public function getCanonicalUrlForLayeredNav($pagevar,$getparams) {

		$categoryId = $this->_registry->registry('current_category')->getId();
		$categoryObj = $this->categoryRepository->get($categoryId);

		$currentUrl = $this->_url->getCurrentUrl();

		$url = $this->_url->getRebuiltUrl($currentUrl);

		$url = str_replace("/index.php", "", $url);

		if (!($this->helper->getPaginationPagesCanonical())) {



			if (sizeof($getparams) == 1 && array_key_exists($pagevar, $getparams)) {
 				
				$url_data = parse_url($url);

				if (isset($url_data['query'])) {
					$url = str_replace('?' . $url_data['query'], '', $url);
				}

				$overrideCanonical = $categoryObj->getOverrideCanonicalUrl();
				$url = $this->generateOverrideCanonicalUrl($overrideCanonical, $url);
 
				$url = $this->getStoreCanonicalUrl($url);
				//$this->getTrailingSlashForCanonicalUrl($url);

			} else if (array_key_exists('cat', $getparams)) {

				$categoryId = $getparams['cat'];
				$category = $this->_categoryFactory->create()->load($categoryId);

				$categoryUrl = $category->getUrl();

				$url_data = parse_url($categoryUrl);
				if (isset($url_data['query'])) {
					$categoryUrl = str_replace('?' . $url_data['query'], '', $categoryUrl);
				}

				unset($getparams['cat']);

				unset($getparams[$pagevar]);

				if (empty($getparams)) {
 
					$overrideCanonical = $categoryObj->getOverrideCanonicalUrl();
					$categoryUrl = $this->generateOverrideCanonicalUrl($overrideCanonical, $categoryUrl);

					$categoryUrl = $this->getStoreCanonicalUrl($categoryUrl);
					$this->getTrailingSlashForCanonicalUrl($categoryUrl);

				} else {



					$categoryUrl = $categoryUrl . '?' . http_build_query($getparams);
 
					$overrideCanonical = $categoryObj->getOverrideCanonicalUrl();
					$categoryUrl = $this->generateOverrideCanonicalUrl($overrideCanonical, $categoryUrl);

					$categoryUrl = $this->getStoreCanonicalUrl($categoryUrl);

					$this->getTrailingSlashForCanonicalUrl($categoryUrl);

				}

			} else {
 
				unset($getparams[$pagevar]);

				$url_data = parse_url($url);

				if (isset($url_data['query'])) {
					$url = str_replace('?' . $url_data['query'], '', $url);
				}

				if (empty($getparams)) {
 
					$overrideCanonical = $categoryObj->getOverrideCanonicalUrl();
					$url = $this->generateOverrideCanonicalUrl($overrideCanonical, $url);

					$url = $this->getStoreCanonicalUrl($url);

					$this->getTrailingSlashForCanonicalUrl($url);

				} else {

					$url = $url . '?' . http_build_query($getparams);

					$overrideCanonical = $categoryObj->getOverrideCanonicalUrl();
					$url = $this->generateOverrideCanonicalUrl($overrideCanonical, $url);

					$url = $this->getStoreCanonicalUrl($url);

					$this->getTrailingSlashForCanonicalUrl($url);

				}

			}

		}

		if ($this->helper->getPaginationPagesCanonical()) {
 
			if (array_key_exists('cat', $getparams)) {

				$categoryId = $getparams['cat'];
				$category = $this->_categoryFactory->create()->load($categoryId);

				$categoryUrl = $category->getUrl();

				$url_data = parse_url($categoryUrl);
				if (isset($url_data['query'])) {
					$categoryUrl = str_replace('?' . $url_data['query'], '', $categoryUrl);
				}

				unset($getparams['cat']);

				if (empty($getparams)) {

					$overrideCanonical = $categoryObj->getOverrideCanonicalUrl();
					$categoryUrl = $this->generateOverrideCanonicalUrl($overrideCanonical, $categoryUrl);

					$categoryUrl = $this->getStoreCanonicalUrl($categoryUrl);
					$this->getTrailingSlashForCanonicalUrl($categoryUrl);

				} else {


					$categoryUrl = $categoryUrl . '?' . http_build_query($getparams);

					$overrideCanonical = $categoryObj->getOverrideCanonicalUrl();
					$categoryUrl = $this->generateOverrideCanonicalUrl($overrideCanonical, $categoryUrl);

					$categoryUrl = $this->getStoreCanonicalUrl($categoryUrl);
					$this->getTrailingSlashForCanonicalUrl($categoryUrl);

				}

			}

			else{       
				if (!(sizeof($getparams) == 1 && array_key_exists($pagevar, $getparams))) {

					$overrideCanonical = $categoryObj->getOverrideCanonicalUrl();
					$url = $this->generateOverrideCanonicalUrl($overrideCanonical, $url);

					$url = $this->getStoreCanonicalUrl($url);
					$this->getTrailingSlashForCanonicalUrl($url);

				}   
			}

		}

	}
 
	public function getCanonicalUrlForCatalog($pagevar,$getparams) {

		$categoryId = $this->_registry->registry('current_category')->getId();
		$categoryObj = $this->categoryRepository->get($categoryId);

		$currentUrl = $this->_url->getCurrentUrl();

		$url = $this->_url->getRebuiltUrl($currentUrl);

		$url = str_replace("/index.php", "", $url);

		if ($this->helper->getPaginationPagesCanonical()) {

			if (empty($getparams)) {
 				

 				$overrideCanonical = $categoryObj->getOverrideCanonicalUrl();
				$url = $this->generateOverrideCanonicalUrl($overrideCanonical, $url);
  
				$url = $this->getStoreCanonicalUrl($url);

				$this->getTrailingSlashForCanonicalUrl($url);

			}

		}

		if (!($this->helper->getPaginationPagesCanonical())) {

			if (sizeof($getparams) == 1 && array_key_exists($pagevar, $getparams)) {
 
				$url_data = parse_url($url);

				if (isset($url_data['query'])) {
					$url = str_replace('?' . $url_data['query'], '', $url);
				}

				$overrideCanonical = $categoryObj->getOverrideCanonicalUrl();
				$url = $this->generateOverrideCanonicalUrl($overrideCanonical, $url);

				$url = $this->getStoreCanonicalUrl($url);
				//$this->getTrailingSlashForCanonicalUrl($url);

			}

			if (empty($getparams)) {
 				
 				$overrideCanonical = $categoryObj->getOverrideCanonicalUrl();
				$url = $this->generateOverrideCanonicalUrl($overrideCanonical, $url);

				$url = $this->getStoreCanonicalUrl($url);

				$this->getTrailingSlashForCanonicalUrl($url);

			}

		}

	}
 	
 	public function generateOverrideCanonicalUrl($overrideCanonical, $url){

		if($overrideCanonical){
			 
			$urlparse = parse_url($url);
			$url_path = $urlparse['path'];
			if(isset($urlparse['query'])){
				$url_query = $urlparse['query'];
				$url = str_replace($url_query,'',$url);
				$url = str_replace('?','',$url);
			}

			$url = str_replace($url_path,'/'.$overrideCanonical,$url);
			
		}

		return $url;
	}

	public function getTrailingSlashForCanonicalUrl($url) {


		if ($this->helper->gettrailingslash() != null) {

			$option = $this->helper->gettrailingslash();

			$urlparts = pathinfo($url);

			if ($option == 'add') {

				if (isset($urlparts['extension'])) {

					$this->pageConfig->addRemotePageAsset(html_entity_decode($url)
						,
						'link_rel',
						['attributes' => ['rel' => 'canonical']]
					);

				} else {

					$lastchar = substr($url, -1);

					if (!($lastchar == '/')) {

						//print_r(rtrim($url,"/").'/');
						$url = rtrim($url, "/") . '/';

						$this->pageConfig->addRemotePageAsset(html_entity_decode($url)
							,
							'link_rel',
							['attributes' => ['rel' => 'canonical']]
						);

					} else {

						$this->pageConfig->addRemotePageAsset(html_entity_decode($url)
							,
							'link_rel',
							['attributes' => ['rel' => 'canonical']]
						);

					}

				}

			}

			if ($option == 'crop') {

				if (isset($urlparts['extension'])) {

					$this->pageConfig->addRemotePageAsset(html_entity_decode($url)
						,
						'link_rel',
						['attributes' => ['rel' => 'canonical']]
					);

				} else {

					$lastchar = substr($url, -1);

					if ($lastchar == '/') {

						//print_r(rtrim($url,"/"));
						$url = rtrim($url, "/");

						$this->pageConfig->addRemotePageAsset(html_entity_decode($url)
							,
							'link_rel',
							['attributes' => ['rel' => 'canonical']]
						);

					} else {

						$this->pageConfig->addRemotePageAsset(html_entity_decode($url)
							,
							'link_rel',
							['attributes' => ['rel' => 'canonical']]
						);
					}

				}

			}
		} else {

			$this->pageConfig->addRemotePageAsset(html_entity_decode($url)
				,
				'link_rel',
				['attributes' => ['rel' => 'canonical']]
			);

		}

	}

	public function getStoreCanonicalUrl($url){

		$storeid = $this->_storeManager->getStore()->getId();

		if ($this->helper->getstorecanonical()!=null){

			if ($this->helper->getcustomurlcanonical()!=null){

				if ($storeid == $this->helper->getstorecanonical()){



					$customurl = $this->helper->getcustomurlcanonical();


					$url_path = parse_url($customurl, PHP_URL_PATH);
					$customurlbase = pathinfo($url_path, PATHINFO_BASENAME);


					$urlparse = parse_url($url);

					$extracted = array_filter(explode("/",parse_url($url,PHP_URL_PATH)));
					$urlbase = current($extracted);


					$newUrl = $customurl;

					if(isset($urlparse['path'])){
						if ($customurlbase == $urlbase){



							$string = $urlparse['path'];
							$find = $customurlbase;
							$replace = '';
							$result = preg_replace("/$find/",$replace,$string,1);

							$newUrl .= $result;
							if (isset($urlparse['query'])){

								$newUrl .= "?".$urlparse['query'];
							}

							$newUrl = preg_replace('/([^:])(\/{2,})/', '$1/', $newUrl);

							return $newUrl;



						}
						else{

							$newUrl .= $urlparse['path'];
							if (isset($urlparse['query'])){

								$newUrl .= "?".$urlparse['query'];
							}  
							return $newUrl;

						}


					}
					else{


						return $newUrl;


					}

				}
				else{


					$storeurl =  $this->_storeManager->getStore($this->helper->getstorecanonical())->getBaseUrl();

					$url_path = parse_url($storeurl, PHP_URL_PATH);
					$storeurlbase = pathinfo($url_path, PATHINFO_BASENAME);


					$urlparse = parse_url($url);

					$extracted = array_filter(explode("/",parse_url($url,PHP_URL_PATH)));
					$urlbase = current($extracted);


					$newUrl = $storeurl;

					if(isset($urlparse['path'])){
						if ($storeurlbase == $urlbase){



							$string = $urlparse['path'];
							$find = $storeurlbase;
							$replace = '';
							$result = preg_replace("/$find/",$replace,$string,1);

							$newUrl .= $result;
							if (isset($urlparse['query'])){

								$newUrl .= "?".$urlparse['query'];
							}

							$newUrl = preg_replace('/([^:])(\/{2,})/', '$1/', $newUrl);

							return $newUrl;



						}
						else{

							$newUrl .= $urlparse['path'];
							if (isset($urlparse['query'])){

								$newUrl .= "?".$urlparse['query'];
							} 
							return $newUrl;


						}


					}
					else{


						return $newUrl;


					}

				}

			}
			else{


				$storeurl =  $this->_storeManager->getStore($this->helper->getstorecanonical())->getBaseUrl();

				$url_path = parse_url($storeurl, PHP_URL_PATH);
				$storeurlbase = pathinfo($url_path, PATHINFO_BASENAME);


				$urlparse = parse_url($url);

				$extracted = array_filter(explode("/",parse_url($url,PHP_URL_PATH)));
				$urlbase = current($extracted);


				$newUrl = $storeurl;

				if(isset($urlparse['path'])){
					if ($storeurlbase == $urlbase){



						$string = $urlparse['path'];
						$find = $storeurlbase;
						$replace = '';
						$result = preg_replace("/$find/",$replace,$string,1);

						$newUrl .= $result;
						if (isset($urlparse['query'])){

							$newUrl .= "?".$urlparse['query'];
						}

						$newUrl = preg_replace('/([^:])(\/{2,})/', '$1/', $newUrl);

						return $newUrl;



					}
					else{

						$newUrl .= $urlparse['path'];
						if (isset($urlparse['query'])){

							$newUrl .= "?".$urlparse['query'];
						} 
						return $newUrl;

					}


				}
				else{


					return $newUrl;


				}
			}
		}

		if ($this->helper->getstorecanonical() == null){

			if ($this->helper->getcustomurlcanonical() == null){

				return $url;

			}
			else{

				$customurl = $this->helper->getcustomurlcanonical();


				$url_path = parse_url($customurl, PHP_URL_PATH);
				$customurlbase = pathinfo($url_path, PATHINFO_BASENAME);


				$urlparse = parse_url($url);

				$extracted = array_filter(explode("/",parse_url($url,PHP_URL_PATH)));
				$urlbase = current($extracted);


				$newUrl = $customurl;

				if(isset($urlparse['path'])){
					if ($customurlbase == $urlbase){



						$string = $urlparse['path'];
						$find = $customurlbase;
						$replace = '';
						$result = preg_replace("/$find/",$replace,$string,1);

						$newUrl .= $result;
						if (isset($urlparse['query'])){

							$newUrl .= "?".$urlparse['query'];
						}

						$newUrl = preg_replace('/([^:])(\/{2,})/', '$1/', $newUrl);

						return $newUrl;



					}
					else{

						$newUrl .= $urlparse['path'];
						if (isset($urlparse['query'])){

							$newUrl .= "?".$urlparse['query'];
						} 
						return $newUrl;

					}


				}
				else{


					return $newUrl;

				}

			}

		}


	}
}
