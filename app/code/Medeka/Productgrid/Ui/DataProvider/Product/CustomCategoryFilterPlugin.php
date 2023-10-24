<?php
namespace Medeka\Productgrid\Ui\DataProvider\Product;

class CustomCategoryFilterPlugin
{
    public function aroundAddFilter(
        \Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider $subject,
        \Closure $proceed,
        \Magento\Framework\Api\Filter $filter
    ) {
        if ($filter->getField() == 'category_id') {
            $collection = $subject->getCollection();
            $collection->addCategoriesFilter(['in' => $filter->getValue()]);
        } else {
            $proceed($filter); // Call the original method for other filters
        }
    }
}
