<?php

namespace Balticode\CategoryConfigurator\Model\Config\Source;

use Exception;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class Category implements ArrayInterface
{
    const TREE_STRUCTURE_SEPARATOR = ' -> ';
    const TREE_STRUCTURE_SEPARATOR_LENGTH = 4;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @param CollectionFactory $collectionFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        ManagerInterface $messageManager
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getCategoryOptionArray($this->getCategoryArray());
    }

    /**
     * @return array
     */
    protected function getCategoryArray()
    {
        $categoryOptions = [];

        try {
            $categoryCollection = $this->collectionFactory->create()->addAttributeToSelect('name');
        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());

            return $categoryOptions;
        }

        /** @var CategoryModel[] $categories */
        $categories = $categoryCollection->getItems();

        foreach ($categories as $category) {
            $categoryOptionString = $this->getCategoryOptionString($category->getPath(), $categories);

            if ($categoryOptionString) {
                $categoryOptions[$category->getEntityId()] = $categoryOptionString;
            }
        }

        return $categoryOptions;
    }

    /**
     * @param string $path
     * @param CategoryModel[] $categories
     * @return bool|string
     */
    protected function getCategoryOptionString($path, $categories)
    {
        $categoriesInPath = explode('/', $path);
        array_shift($categoriesInPath);

        if (count($categoriesInPath) < 1) {
            return false;
        }

        $categoryOptionString = '';

        foreach ($categoriesInPath as $categoryId) {
            $categoryOptionString .= $this->getCategoryName($categories, $categoryId) . self::TREE_STRUCTURE_SEPARATOR;
        }

        return substr($categoryOptionString, 0, strlen($categoryOptionString) - self::TREE_STRUCTURE_SEPARATOR_LENGTH);
    }

    /**
     * @param CategoryModel[] $categories
     * @param string $categoryId
     * @return string
     */
    protected function getCategoryName($categories, $categoryId)
    {
        foreach ($categories as $category) {
            if ($category->getEntityId() == $categoryId) {
                return $category->getName();
            }
        }

        return '';
    }

    /**
     * @param array $categories
     * @return array
     */
    protected function getCategoryOptionArray($categories)
    {
        $options = [];

        foreach ($categories as $key => $category) {
            $options[] = ['label' => $category, 'value' => $key];
        }

        return $options;
    }
}