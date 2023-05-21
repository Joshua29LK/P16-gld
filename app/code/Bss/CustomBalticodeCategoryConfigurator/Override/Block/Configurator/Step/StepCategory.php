<?php

namespace Bss\CustomBalticodeCategoryConfigurator\Override\Block\Configurator\Step;

use Balticode\CategoryConfigurator\Api\ConfiguratorRepositoryInterface;
use Balticode\CategoryConfigurator\Api\Data\StepInterface;
use Balticode\CategoryConfigurator\Helper\Config\GeneralConfig;
use Balticode\CategoryConfigurator\Model\CurrencySymbolProvider;
use Balticode\CategoryConfigurator\Model\Product\DataProvider as ProductDataProvider;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;

class StepCategory extends \Balticode\CategoryConfigurator\Block\Configurator\Step\StepCategory
{
    public function __construct(
        Context $context,
        StepInterface $step,
        ConfiguratorRepositoryInterface $configuratorRepository,
        GeneralConfig $generalConfig,
        CurrencySymbolProvider $currencySymbolProvider,
        CategoryFactory $categoryFactory,
        ProductCollectionFactory $productCollectionFactory,
        ProductDataProvider $productDataProvider,
        \Magento\CatalogInventory\Helper\Stock $stockFilter,
        array $data = []
    ) {
        $this->stockFilter = $stockFilter;

        parent::__construct(
            $context,
            $step,
            $configuratorRepository,
            $generalConfig,
            $currencySymbolProvider,
            $categoryFactory,
            $productCollectionFactory,
            $productDataProvider,
            $data
        );
    }

    /**
     * @var string
     */
    protected $_template = 'Balticode_CategoryConfigurator::configurator/step/category.phtml';

    /**
     * @return ProductCollection
     */
    protected function getProductCollection()
    {
        parent::getProductCollection();

        $this->productCollection->setOrder('sort_order_in_step','ASC');
        $this->stockFilter->addInStockFilterToCollection($this->productCollection);

        return $this->productCollection;
    }
}