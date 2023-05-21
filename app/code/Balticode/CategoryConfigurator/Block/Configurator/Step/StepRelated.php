<?php

namespace Balticode\CategoryConfigurator\Block\Configurator\Step;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Exception\NoSuchEntityException;

class StepRelated extends AbstractStep
{
    const RELATED_PRODUCTS_ACTION_PATH = 'category/configurator_step/relatedProducts';

    /**
     * @var string
     */
    protected $_template = 'configurator/step/related.phtml';

    /**
     * @return string
     */
    public function getRelatedProductsActionUrl()
    {
        return $this->getUrl(self::RELATED_PRODUCTS_ACTION_PATH);
    }
}