<?php
namespace Bss\HarrigoEverCrumbs\Block;

class Evercrumbs extends \Harrigo\EverCrumbs\Block\Evercrumbs
{
	/**
     * @return array
     */
	public function getCrumbs()
    {
		$evercrumbs = [];
		
		$evercrumbs[] = [
			'label' => __('Home'),
			'title' => __('Go to Home Page'),
			'link' => $this->_storeManager->getStore()->getBaseUrl()
		];

		$path = $this->_catalogData->getBreadcrumbPath();
		$product = $this->registry->registry('current_product');
		$categoryCollection = clone $product->getCategoryCollection();
		$categoryCollection->clear();
		$categoryCollection->addAttributeToSort('level', $categoryCollection::SORT_ORDER_DESC)->addAttributeToFilter('path', array('like' => "1/" . $this->_storeManager->getStore()->getRootCategoryId() . "/%"));
		$categoryCollection->setPageSize(1);
		$breadcrumbCategories = $categoryCollection->getFirstItem()->getParentCategories();
		$categorieKeys = array_keys($breadcrumbCategories);
		$lastCategoryKey = end($categorieKeys);

		foreach ($breadcrumbCategories as $categoryId => $category) {
			if ($categoryId == $lastCategoryKey && $category->getProductCollection()->count() == 1) {
				break;
			}
			$evercrumbs[] = [
				'label' => $category->getName(),
				'title' => $category->getName(),
				'link' => $category->getUrl(),
				'id' => $category->getProductCount()
			];
		}

		$evercrumbs[] = [
				'label' => $product->getName(),
				'title' => $product->getName(),
				'link' => ''
			];
				
		return $evercrumbs;
    }
}