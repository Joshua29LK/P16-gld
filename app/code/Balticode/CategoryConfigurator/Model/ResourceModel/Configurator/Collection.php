<?php

namespace Balticode\CategoryConfigurator\Model\ResourceModel\Configurator;

use Balticode\CategoryConfigurator\Model\Configurator;
use Balticode\CategoryConfigurator\Model\ResourceModel\Configurator as ConfiguratorResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Configurator::class, ConfiguratorResource::class);
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('configurator_id', 'title');
    }
}
