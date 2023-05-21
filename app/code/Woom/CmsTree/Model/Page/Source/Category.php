<?php

namespace Woom\CmsTree\Model\Page\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Framework\Exception\LocalizedException;

class Category implements OptionSourceInterface
{
    /**
     * Context
     *
     * @var ContextInterface
     */
    private $context;

    /**
     * Category collection factory
     *
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * Category tree
     *
     * @var array
     */
    private $categoryTree;

    /**
     * Category constructor.
     *
     * @param ContextInterface          $context
     * @param CategoryCollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        ContextInterface $context,
        CategoryCollectionFactory $categoryCollectionFactory
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getCategoryTree();
    }

    /**
     * Get category tree for options
     *
     * @return array
     * @throws LocalizedException
     */
    protected function getCategoryTree()
    {
        if ($this->categoryTree === null) {
            $storeId = $this->context->getRequestParam('store');
            /** @var CategoryCollection $collection */
            $collection = $this->categoryCollectionFactory->create();

            $collection->addAttributeToSelect('name')
                        ->addAttributeToSelect('level')
                        ->addAttributeToFilter(
                            'entity_id',
                            ['neq' => CategoryModel::TREE_ROOT_ID]
                        )
                        ->addAttributeToFilter('include_in_menu', 1)
                        ->addOrder('path', $collection::SORT_ORDER_ASC)
            ;

            if ($storeId) {
                $collection->setStoreId($storeId);
            }

            $options = [];
            foreach ($collection as $category) {
                $repeat = $category->getLevel() - 1;
                $label = sprintf('%s %s', str_repeat('-', $repeat), $category->getName());
                $value = $category->getLevel() >= 2 ? $category->getId() : null;
                $options[$category->getId()]['value'] = $value;
                $options[$category->getId()]['label'] = $label;
            }

            $this->categoryTree = $options;
        }

        return $this->categoryTree;
    }
}
