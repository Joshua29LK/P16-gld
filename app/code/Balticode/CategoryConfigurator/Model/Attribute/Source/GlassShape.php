<?php

namespace Balticode\CategoryConfigurator\Model\Attribute\Source;

use Balticode\CategoryConfigurator\Helper\Config\GeneralConfig;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class GlassShape extends AbstractSource
{
    /**
     * @var GeneralConfig
     */
    protected $generalConfig;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @param GeneralConfig $generalConfig
     * @param ProductCollectionFactory $productCollectionFactory
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        GeneralConfig $generalConfig,
        ProductCollectionFactory $productCollectionFactory,
        CategoryFactory $categoryFactory
    ) {
        $this->generalConfig = $generalConfig;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllOptions()
    {
        $options = [['label' => 'No glass shape', 'value' => 0]];

        $glassShapes = $this->getGlassShapes();

        foreach ($glassShapes as $glassShape) {
            $options[] = [
                'label' => $glassShape->getName(),
                'value' => $glassShape->getEntityId()
            ];
        }

        return $options;
    }

    /**
     * @return array|ProductCollection
     */
    protected function getGlassShapes()
    {
        $categoryId = $this->generalConfig->getGlassShapesCategory();

        if (!$categoryId) {
            return [];
        }

        $category = $this->categoryFactory->create();
        $category->setId($categoryId);

        $collection = $this->productCollectionFactory->create();
        $collection->addFieldToSelect(ProductInterface::NAME);
        $collection->addCategoryFilter($category);

        return $collection;
    }
}
