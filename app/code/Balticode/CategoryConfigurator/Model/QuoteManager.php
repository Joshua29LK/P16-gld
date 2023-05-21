<?php

namespace Balticode\CategoryConfigurator\Model;

use Balticode\CategoryConfigurator\Model\Validator\StepsData;
use Balticode\CategoryConfigurator\Setup\InstallData;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Api\AttributeValue;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;
use Magento\Catalog\Model\Product\Option;

class QuoteManager
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var Configurable
     */
    protected $configurable;

    /**
     * @var Option
     */
    protected $productOptions;

    /**
     * @var GlassShapeService
     */
    protected $glassShapeService;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param DataObjectFactory $dataObjectFactory
     * @param GlassShapeService $glassShapeService
     * @param Configurable $configurable
     * @param Option $productOptions
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        DataObjectFactory $dataObjectFactory,
        GlassShapeService $glassShapeService,
        Configurable $configurable,
        Option $productOptions
    ) {
        $this->productRepository = $productRepository;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->configurable = $configurable;
        $this->productOptions = $productOptions;
        $this->glassShapeService = $glassShapeService;
    }

    /**
     * @param Quote $quote
     * @param array $stepProducts
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function addStepProductsToQuote($quote, $stepProducts)
    {
        foreach ($stepProducts as $stepProduct) {
            $this->addStepProductToQuote($quote, $stepProduct);
        }
    }

    /**
     * @param Quote $quote
     * @param array $stepProduct
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @return void
     */
    protected function addStepProductToQuote($quote, $stepProduct)
    {
        if (!isset($stepProduct[StepsData::ARRAY_INDEX_PRODUCT_ID])) {
            return;
        }

        $productId = $stepProduct[StepsData::ARRAY_INDEX_PRODUCT_ID];
        $product = $this->productRepository->getById($productId);

        if (isset($stepProduct[StepsData::ARRAY_INDEX_SELECTED_CONFIGURATION])) {
            $this->addGlassShapeToQuote($quote, $stepProduct, $product);

            return;
        }

        if (isset($stepProduct[StepsData::ARRAY_INDEX_SELECTED_OPTIONS])) {
            $this->addOptionedProductToQuote($quote, $stepProduct, $product);

            return;
        }

        $quote->addProduct($product);
    }

    /**
     * @param Quote $quote
     * @param array $stepProduct
     * @param ProductInterface $product
     * @throws NoSuchEntityException
     * @return void
     * @throws LocalizedException
     */
    protected function addGlassShapeToQuote($quote, $stepProduct, $product)
    {
        if (!isset($stepProduct[StepsData::ARRAY_INDEX_SELECTED_CONFIGURATION])) {
            return;
        }

        $configuredProductId = $stepProduct[StepsData::ARRAY_INDEX_SELECTED_CONFIGURATION];
        $configurableOptions = $this->getGlassShapeConfigurableOptions($configuredProductId, $product);
        $dimensionOptions = $this->getGlassShapeDimensionOptions($stepProduct, $product);

        $productConfigurations = $this->dataObjectFactory->create();
        $productConfigurations->setData('qty', $this->glassShapeService->calculateQty($stepProduct));
        $productConfigurations->setData('options', $dimensionOptions);

        if ($configurableOptions) {
            $productConfigurations->setData('super_attribute', $configurableOptions);
        }

        $quote->addProduct($product, $productConfigurations);
    }

    /**
     * @param int $configuredProductId
     * @param ProductInterface $parentProduct
     * @return null|array
     * @throws NoSuchEntityException
     */
    protected function getGlassShapeConfigurableOptions($configuredProductId, $parentProduct)
    {
        $configuredProduct = $this->productRepository->getById($configuredProductId);
        $glassShapeTypeAttribute = $configuredProduct->getCustomAttribute(InstallData::GLASS_TYPE);

        if (!$glassShapeTypeAttribute instanceof AttributeValue) {
            return null;
        }

        $glassShapeType = $glassShapeTypeAttribute->getValue();
        $configurableOptions = [];
        $configurableAttributes = $this->configurable->getConfigurableAttributes($parentProduct);

        foreach ($configurableAttributes as $configurableAttribute) {
            $productAttribute = $configurableAttribute->getProductAttribute();

            if ($productAttribute->getAttributeCode() == InstallData::GLASS_TYPE) {
                $configurableOptions[$configurableAttribute->getAttributeId()] = $glassShapeType;
            }
        }

        return $configurableOptions;
    }

    /**
     * @param array $stepProduct
     * @param ProductInterface $product
     * @return array
     */
    protected function getGlassShapeDimensionOptions($stepProduct, $product)
    {
        $glassShapeOptions = $this->productOptions->getProductOptionCollection($product)->getItems();
        $dimensionOptions = [];

        foreach ($glassShapeOptions as $option) {
            if ($optionValue = $this->glassShapeService->getResolvedDimensionOptionValue($stepProduct, $option)) {
                $dimensionOptions[$optionValue['id']] = $optionValue['value'];
            }
        }

        return $dimensionOptions;
    }

    /**
     * @param Quote $quote
     * @param array $stepProduct
     * @param ProductInterface $product
     * @throws LocalizedException
     */
    protected function addOptionedProductToQuote($quote, $stepProduct, $product)
    {
        if (!isset($stepProduct[StepsData::ARRAY_INDEX_SELECTED_OPTIONS])) {
            return;
        }

        $productOptions = [];

        foreach ($stepProduct[StepsData::ARRAY_INDEX_SELECTED_OPTIONS] as $productOption) {
            if (!isset($productOption[StepsData::ARRAY_INDEX_OPTION_ID]) ||
                !isset($productOption[StepsData::ARRAY_INDEX_OPTION_TYPE_ID])
            ) {
                continue;
            }

            $optionId = $productOption[StepsData::ARRAY_INDEX_OPTION_ID];
            $optionTypeId = $productOption[StepsData::ARRAY_INDEX_OPTION_TYPE_ID];
            $productOptions[$optionId] = $optionTypeId;
        }

        $productConfigurations = $this->dataObjectFactory->create();
        $productConfigurations->setData('options', $productOptions);

        $quote->addProduct($product, $productConfigurations);
    }
}