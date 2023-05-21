<?php

namespace Balticode\CategoryConfigurator\Controller\Configurator\Step;

use Balticode\CategoryConfigurator\Controller\Configurator\Cart;
use Balticode\CategoryConfigurator\Model\Product\DataProvider as ProductDataProvider;
use Balticode\CategoryConfigurator\Helper\SearchCriteria\Product as ProductSearchCriteria;
use Balticode\CategoryConfigurator\Model\Validator\Product;
use Balticode\CategoryConfigurator\Model\Validator\ValidatorInterface;
use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Zend_Json;

class RelatedProducts extends Action
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ProductSearchCriteria
     */
    protected $productSearchCriteria;

    /**
     * @var ProductDataProvider
     */
    protected $productDataProvider;

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * @param Context $context
     * @param ValidatorInterface $validator
     * @param ProductRepositoryInterface $productRepository
     * @param ProductSearchCriteria $productSearchCriteria
     * @param ProductDataProvider $productDataProvider
     * @param JsonFactory $jsonResultFactory
     */
    public function __construct(
        Context $context,
        ValidatorInterface $validator,
        ProductRepositoryInterface $productRepository,
        ProductSearchCriteria $productSearchCriteria,
        ProductDataProvider $productDataProvider,
        JsonFactory $jsonResultFactory
    ) {
        $this->validator = $validator;
        $this->productRepository = $productRepository;
        $this->productSearchCriteria = $productSearchCriteria;
        $this->productDataProvider = $productDataProvider;
        $this->jsonResultFactory = $jsonResultFactory;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        $result->setData([]);

        try {
            $ids = $this->processRelatedProductIdsRequest();
        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage(__($exception->getMessage()));
            $result->setHttpResponseCode(400);

            return $result;
        }

        if (!$ids) {
            return $result;
        }

        $searchCriteria = $this->productSearchCriteria->getProductsByListOfIdsSearchCriteria($ids);
        $relatedProducts = $this->productRepository->getList($searchCriteria)->getItems();
        $productsData = $this->productDataProvider->prepareProductsData($relatedProducts);
        $result->setData($productsData);

        return $result;
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function processRelatedProductIdsRequest()
    {
        if (!$this->getRequest()->isAjax()) {
            throw new Exception(Cart::ERROR_MESSAGE_FAILED_REQUEST);
        }

        $postValues = (array) $this->getRequest()->getPost();

        if (!$this->validator->validate($postValues)) {
            throw new Exception(Cart::ERROR_MESSAGE_INCORRECT_PARAMETERS);
        }

        $productId = $postValues[Product::ARRAY_INDEX_PRODUCT];

        try {
            $selectedProduct = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            throw new Exception(Cart::ERROR_MESSAGE_PRODUCT_NOT_FOUND);
        }

        return $selectedProduct->getRelatedProductIds();
    }
}