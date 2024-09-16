<?php
namespace Bss\CustomTheme\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\CategoryManagementInterface;
class MegaMenu extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var CategoryManagementInterface
     */
    protected $categoryManagement;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @param Template\Context $context
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CategoryManagementInterface $categoryManagement
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CategoryRepositoryInterface $categoryRepository,
        CategoryManagementInterface $categoryManagement,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        array $data = []
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryManagement = $categoryManagement;
        $this->productRepository = $productRepository;
        parent::__construct($context, $data);
    }

    /**
     * @param $categoryId
     * @return CategoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryById()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $categoryId = $this->getData('category_id');
        return $this->categoryRepository->get($categoryId, $storeId);
    }

    /**
     * Get Level 1 Child Categories by Parent ID
     *
     * @param int $parentId
     * @return \Magento\Catalog\Api\Data\CategoryInterface[]
     */
    public function getLevel1ChildCategories()
    {
        $categories = [];
        $parentId = $this->getData('category_id');
        $storeId = $this->_storeManager->getStore()->getId();
        try {
            /** @var CategoryInterface $category */
            $category = $this->categoryRepository->get($parentId, $storeId);
            $childCategories = $category->getChildrenCategories();
            foreach ($childCategories as $childCategory) {
                $categories[] = $childCategory;
            }
        } catch (\Exception $e) {
            // Handle the exception
        }

        return $categories;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getTopProducts()
    {
        $products = [];
        try {
            $productSkus = $this->getData('product_skus');
            if ($productSkus) {
                $productSkuArray = explode(',', $productSkus);
                foreach ($productSkuArray as $sku) {
                    $product = $this->productRepository->getById($sku);
                    $products[] = $product;
                }
            }
        } catch (\Exception $e) {
            return [];
        }
        return $products;
    }
}
