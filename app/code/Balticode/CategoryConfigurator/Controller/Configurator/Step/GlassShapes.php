<?php

namespace Balticode\CategoryConfigurator\Controller\Configurator\Step;

use Balticode\CategoryConfigurator\Controller\Configurator\Cart;
use Balticode\CategoryConfigurator\Helper\SearchCriteria\Product as ProductSearchCriteria;
use Balticode\CategoryConfigurator\Model\Product\DataProvider as ProductDataProvider;
use Balticode\CategoryConfigurator\Model\Product\GlassShapesProvider;
use Balticode\CategoryConfigurator\Model\Validator\Product;
use Balticode\CategoryConfigurator\Model\Validator\ValidatorInterface;
use Balticode\CategoryConfigurator\Setup\InstallData;
use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Zend_Json;

class GlassShapes extends Action
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
     * @var GlassShapesProvider
     */
    protected $glassShapesProvider;

    /**
     * @param Context $context
     * @param ValidatorInterface $validator
     * @param ProductRepositoryInterface $productRepository
     * @param ProductSearchCriteria $productSearchCriteria
     * @param ProductDataProvider $productDataProvider
     * @param GlassShapesProvider $glassShapesProvider
     */
    public function __construct(
        Context $context,
        ValidatorInterface $validator,
        ProductRepositoryInterface $productRepository,
        ProductSearchCriteria $productSearchCriteria,
        ProductDataProvider $productDataProvider,
        GlassShapesProvider $glassShapesProvider
    ) {
        $this->validator = $validator;
        $this->productRepository = $productRepository;
        $this->productSearchCriteria = $productSearchCriteria;
        $this->productDataProvider = $productDataProvider;
        $this->glassShapesProvider = $glassShapesProvider;

        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $ids = $this->processGlassShapeIdsRequest();
        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage(__($exception->getMessage()));

            return;
        }

        $searchCriteria = $this->productSearchCriteria->getProductsByListOfIdsSearchCriteria($ids);
        $glassShapes = $this->productRepository->getList($searchCriteria)->getItems();
        $productsData = $this->productDataProvider->prepareProductsData($glassShapes);
        $productsData = $this->glassShapesProvider->appendDefaultGlassShapesDimensions($productsData);

        $this->getResponse()->representJson(Zend_Json::encode($productsData));
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function processGlassShapeIdsRequest()
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

        $glassShapesAttribute = $selectedProduct->getCustomAttribute(InstallData::GLASS_SHAPES);

        if (!$glassShapesAttribute instanceof AttributeInterface || !$ids = $glassShapesAttribute->getValue()) {
            throw new Exception(Cart::ERROR_MESSAGE_PRODUCT_NOT_FOUND);
        }

        return $ids;
    }
}