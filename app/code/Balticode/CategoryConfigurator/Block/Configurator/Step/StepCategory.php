<?php

namespace Balticode\CategoryConfigurator\Block\Configurator\Step;

use Balticode\CategoryConfigurator\Api\ConfiguratorRepositoryInterface;
use Balticode\CategoryConfigurator\Api\Data\StepInterface;
use Balticode\CategoryConfigurator\Helper\Config\GeneralConfig;
use Balticode\CategoryConfigurator\Model\CurrencySymbolProvider;
use Balticode\CategoryConfigurator\Model\Product\DataProvider as ProductDataProvider;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Zend_Json;

class StepCategory extends AbstractStep
{
    /**
     * @var string
     */
    protected $_template = 'configurator/step/category.phtml';

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var ProductCollection
     */
    protected $productCollection;

    /**
     * @var ProductDataProvider
     */
    protected $productDataProvider;

    /**
     * @param Context $context
     * @param StepInterface $step
     * @param ConfiguratorRepositoryInterface $configuratorRepository
     * @param GeneralConfig $generalConfig
     * @param CurrencySymbolProvider $currencySymbolProvider
     * @param CategoryFactory $categoryFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param ProductDataProvider $productDataProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        StepInterface $step,
        ConfiguratorRepositoryInterface $configuratorRepository,
        GeneralConfig $generalConfig,
        CurrencySymbolProvider $currencySymbolProvider,
        CategoryFactory $categoryFactory,
        ProductCollectionFactory $productCollectionFactory,
        ProductDataProvider $productDataProvider,
        array $data = []
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productDataProvider = $productDataProvider;

        parent::__construct($context, $step, $configuratorRepository, $generalConfig, $currencySymbolProvider, $data);
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        $id = $this->step->getCategoryId();

        if (!$id) {
            return [];
        }

        $collection = $this->getProductCollection();
        $collection->addCategoryFilter($this->getCategory());

        $productData = $this->productDataProvider->prepareProductsData($collection->getItems());

        return $productData;
    }

    /**
     * @param array $products
     * @return string
     */
    public function getProductDataJson($products)
    {
        return Zend_Json::encode($products);
    }

    /**
     * @return Category
     */
    protected function getCategory()
    {
        $category = $this->categoryFactory->create();
        $category->setId($this->step->getCategoryId());

        return $category;
    }

    /**
     * @return ProductCollection
     */
    protected function getProductCollection()
    {
        if (null !== $this->productCollection) {
            return $this->productCollection;
        }

        $this->productCollection = $this->productCollectionFactory->create();
        $this->productCollection->addFieldToSelect(ProductInterface::NAME);
        $this->productCollection->addFieldToSelect(ProductInterface::PRICE);
        $this->productCollection->addAttributeToSelect('description');
        $this->productCollection->addAttributeToSelect('image');

        return $this->productCollection;
    }
}