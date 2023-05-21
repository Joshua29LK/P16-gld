<?php
namespace Bss\CustomBalticodeCategoryConfigurator\Override;

use Balticode\CategoryConfigurator\Model\Validator\StepsData;
use Balticode\CategoryConfigurator\Model\GlassShapeService;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\Product\Option;

class QuoteManager extends \Balticode\CategoryConfigurator\Model\QuoteManager
{
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
        Option $productOptions,
        \Magento\Bundle\Model\Product\Type $bundleType,
        \Magento\Bundle\Model\Option $bundleOption
    ) {
        parent::__construct(
            $productRepository,
            $dataObjectFactory,
            $glassShapeService,
            $configurable,
            $productOptions
        );

        $this->bundleType = $bundleType;
        $this->bundleOption = $bundleOption;
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
        $params = [];
        if ($product->getTypeId() == 'bundle') {
            $options = $this->bundleOption->getResourceCollection()->setProductIdFilter($product->getId())->setPositionOrder();
            $options->joinValues($product->getStoreId());
            $selections = $this->bundleType->getSelectionsCollection($this->bundleType->getOptionsIds($product), $product);
            if (count($selections) > 0) {
                foreach ($selections as $selection) {
                    if ($selection->getIsDefault()) {
                        $params['bundle_option'][$selection->getOptionId()][$selection->getSelectionId()] = $selection->getSelectionId();
                    }
                }
            }
            $productConfigurations = $this->dataObjectFactory->create();
            $productConfigurations->setData($params);
            $quote->addProduct($product, $productConfigurations);
        } else {
            $quote->addProduct($product);
        }
    }
}