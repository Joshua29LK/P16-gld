<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_EasycatalogimgNSideBarCustomize
 * @author     Extension Team
 * @copyright  Copyright (c) 2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\EasycatalogimgNSideBarCustomize\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;

/**
 * Class SubcategoriesList
 *
 * @package Bss\SwissupEasycatalogimgCustomize\Block
 */
class SubcategoriesList extends \Swissup\Easycatalogimg\Block\SubcategoriesList
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    protected $storeManager;

    /**
     * SubcategoriesList constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Swissup\Easycatalogimg\Helper\Config $configHelper
     * @param \Magento\Framework\Registry $registry
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Swissup\Easycatalogimg\Helper\Image $imageHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Framework\File\Mime $mime
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Swissup\Easycatalogimg\Helper\Config $configHelper,
        \Magento\Framework\Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        \Swissup\Easycatalogimg\Helper\Image $imageHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\File\Mime $mime,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct(
            $context,
            $configHelper,
            $registry,
            $categoryRepository,
            $imageHelper,
            $layerResolver,
            $mime,
            $data
        );
    }

    /**
     * Get product url
     *
     * Active when category have only one product
     *
     * @param CategoryInterface $category
     * @return string
     */
    public function getProductUrl($category)
    {
        if ($category instanceof \Magento\Catalog\Model\Category) {
            $products = $this->getProductsByCategory($category->getId());
        } else {
            $products = $this->getProductsByCategory($category->getEntityId());
        }
        
        if ($products->count() == 1) {
            $product = $products->getFirstItem();
            return $product->getProductUrl();
        }
        if (!$category instanceof \Magento\Catalog\Model\Category) {
            $category = $this->categoryRepository->get($category->getEntityId());
        }
        return $category->getUrl();
    }

    /**
     * Get products by category id
     *
     * @param int $cateId
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function getProductsByCategory($cateId)
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addCategoriesFilter(['eq' => $cateId]);
        $collection->addUrlRewrite($cateId);
        return $collection;
    }
}
