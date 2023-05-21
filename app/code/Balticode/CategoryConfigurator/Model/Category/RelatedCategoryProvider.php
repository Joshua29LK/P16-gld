<?php

namespace Balticode\CategoryConfigurator\Model\Category;

use Magento\Catalog\Api\Data\CategoryInterface;

class RelatedCategoryProvider
{
    /**
     * @param CategoryInterface $category
     * @return string|null
     */
    public function getRelatedCategoryIds($category)
    {
        if (!$category instanceof CategoryInterface) {
            return null;
        }

        $categoryIds = '';
        $categoryId = $category->getId();
        $childCategories = $category->getChildren();

        if (!$categoryId) {
            return null;
        }

        $categoryIds .= $categoryId;

        if ($childCategories) {
            $categoryIds .= ',' . $childCategories;
        }

        return $categoryIds;
    }
}