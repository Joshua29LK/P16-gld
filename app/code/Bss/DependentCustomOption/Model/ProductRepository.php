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
 * @package    Bss_DependentCustomOption
 * @author     Extension Team
 * @copyright  Copyright (c) 2020-2021 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\DependentCustomOption\Model;

use Bss\DependentCustomOption\Api\Data\DependentOptionInterface;
use Bss\DependentCustomOption\Api\Data\ProductInterface;
use Bss\DependentCustomOption\Api\Data\ProductSearchResultsInterfaceFactory;
use Bss\DependentCustomOption\Api\DependentOptionRepositoryInterface;
use Bss\DependentCustomOption\Model\CustomOptions\Converter;
use Bss\DependentCustomOption\Model\CustomOptions\DcoProcessor;
use Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory;
use Magento\Catalog\Api\Data\ProductCustomOptionValuesInterfaceFactory;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;

class ProductRepository implements \Bss\DependentCustomOption\Api\ProductRepositoryInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ProductInterfaceFactory
     */
    protected $coreProductTemplate;

    /**
     * @var DependentOptionRepositoryInterface
     */
    protected $dependentOptionRepository;

    /**
     * @var \Bss\DependentCustomOption\Api\Data\ProductInterfaceFactory
     */
    protected $dcoProductTemplate;

    /**
     * @var Converter
     */
    protected $converter;

    /**
     * @var DcoProcessor
     */
    protected $dcoProcessor;

    /**
     * @var ProductCustomOptionInterfaceFactory
     */
    protected $coreCustomOptionTemplate;

    /**
     * @var ProductCustomOptionValuesInterfaceFactory
     */
    protected $coreCustomOptionValuesTemplate;

    /**
     * @var ProductSearchResultsInterfaceFactory
     */
    protected $productSearchResultsFactory;

    /**
     * ProductRepository constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param ProductInterfaceFactory $coreProductTemplate
     * @param DependentOptionRepositoryInterface $dependentOptionRepository
     * @param \Bss\DependentCustomOption\Api\Data\ProductInterfaceFactory $dcoProductTemplate
     * @param Converter $converter
     * @param DcoProcessor $dcoProcessor
     * @param ProductCustomOptionInterfaceFactory $coreCustomOptionTemplate
     * @param ProductCustomOptionValuesInterfaceFactory $coreCustomOptionValuesTemplate
     * @param ProductSearchResultsInterfaceFactory $productSearchResultsFactory
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductInterfaceFactory $coreProductTemplate,
        DependentOptionRepositoryInterface $dependentOptionRepository,
        \Bss\DependentCustomOption\Api\Data\ProductInterfaceFactory $dcoProductTemplate,
        Converter $converter,
        DcoProcessor $dcoProcessor,
        ProductCustomOptionInterfaceFactory $coreCustomOptionTemplate,
        ProductCustomOptionValuesInterfaceFactory $coreCustomOptionValuesTemplate,
        ProductSearchResultsInterfaceFactory $productSearchResultsFactory
    ) {
        $this->productRepository = $productRepository;
        $this->coreProductTemplate = $coreProductTemplate;
        $this->dependentOptionRepository = $dependentOptionRepository;
        $this->dcoProductTemplate = $dcoProductTemplate;
        $this->converter = $converter;
        $this->dcoProcessor = $dcoProcessor;
        $this->coreCustomOptionTemplate = $coreCustomOptionTemplate;
        $this->coreCustomOptionValuesTemplate = $coreCustomOptionValuesTemplate;
        $this->productSearchResultsFactory = $productSearchResultsFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(ProductInterface $dcoProduct)
    {
        return $this->saveBySku($dcoProduct->getSku(), $dcoProduct);
    }

    /**
     * @inheritDoc
     */
    public function saveBySku(
        string $sku,
        ProductInterface $dcoProduct
    ) {
        if (!$sku) {
            throw new InputException(__('Invalid params: SKU'));
        }
        /** @var DependentOptionInterface[] $dcoProductOptions */
        $dcoProductOptions = $dcoProduct->getOptions();

        try {
            $product = $this->productRepository->get($sku);
        } catch (NoSuchEntityException $exception) {
            $product = $this->coreProductTemplate->create();
        }
        $this->converter->convertProduct($dcoProduct, $product);
        $productAfter = $this->productRepository->save($product);
        foreach ($dcoProductOptions as $dcoProductOption) {
            $dcoProductOption->setProductSku($product->getSku());
            $this->dcoProcessor->saveOption($dcoProductOption);
        }

        /** @var ProductInterface $dcoProductResult */
        $dcoProductResult = $this->dcoProductTemplate->create();
        $this->converter->convertProduct($productAfter, $dcoProductResult);
        $dcoProductResult->setOptions(
            $this->dependentOptionRepository->getListBySku($product->getSku())
        );
        return $dcoProductResult;
    }

    /**
     * @inheritDoc
     */
    public function get(string $sku)
    {
        $product = $this->productRepository->get($sku);
        $dcoOptions = $this->dependentOptionRepository->getListBySku($sku);
        /** @var ProductInterface $dcoProductResult */
        $dcoProductResult = $this->dcoProductTemplate->create();
        $this->converter->convertProduct($product, $dcoProductResult);
        $dcoProductResult->setOptions(
            $dcoOptions
        );
        return $dcoProductResult;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $listProduct = $this->productRepository->getList($searchCriteria);
        $items = $listProduct->getItems();
        $dcoItemsResult = [];
        foreach ($items as $item) {
            $dcoOptions = $this->dependentOptionRepository->getListBySku($item->getSku());
            /** @var ProductInterface $dcoProductResult */
            $dcoProductResult = $this->dcoProductTemplate->create();
            $this->converter->convertProduct($item, $dcoProductResult);
            $dcoProductResult->setOptions(
                $dcoOptions
            );
            $dcoItemsResult[] = $dcoProductResult;
        }
        $searchItemsResult = $this->productSearchResultsFactory->create();
        $searchItemsResult->setSearchCriteria($searchCriteria);
        $searchItemsResult->setItems($dcoItemsResult);
        $searchItemsResult->setTotalCount($listProduct->getTotalCount());
        return $searchItemsResult;
    }
}
