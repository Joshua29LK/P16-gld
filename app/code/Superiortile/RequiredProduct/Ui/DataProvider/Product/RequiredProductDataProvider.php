<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Superiortile\RequiredProduct\Ui\DataProvider\Product;

use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Ui\DataProvider\Product\Related\AbstractDataProvider;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Superiortile\RequiredProduct\Ui\DataProvider\Product\RequiredProductDataProvider
 */
class RequiredProductDataProvider extends AbstractDataProvider
{
    /**
     * Get Link Type
     *
     * @return string
     */
    protected function getLinkType()
    {
        return '';
    }

    /**
     * Get Collection
     *
     * @return Collection|AbstractCollection
     */
    public function getCollection()
    {
        /** @var Collection $collection */
        $collection = parent::getCollection();
        $collection->addAttributeToFilter('type_id', 'simple');
        $collection->addAttributeToSelect('status');
        if ($this->getStore()) {
            $collection->setStore($this->getStore());
        }
        if (!$this->getProduct()) {
            return $collection;
        }
        $collection->addAttributeToFilter(
            $collection->getIdFieldName(),
            ['nin' => [$this->getProduct()->getId()]]
        );
        $collection->setVisibility(
            $this->getVisibleInSiteIds()
        );
        return $this->addCollectionFilters($collection);
    }

    /**
     * Return visible site ids
     *
     * @return array
     */
    private function getVisibleInSiteIds()
    {
        return [
            Visibility::VISIBILITY_IN_SEARCH,
            Visibility::VISIBILITY_IN_CATALOG,
            Visibility::VISIBILITY_BOTH
        ];
    }
}
