<?php

namespace Balticode\CategoryConfigurator\Model\ResourceModel\Step;

use Balticode\CategoryConfigurator\Model\ResourceModel\Step as StepResource;
use Balticode\CategoryConfigurator\Model\Step;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Step::class, StepResource::class);
    }

    /**
     * @return void
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->addFilterToMap('configurator_id', 'main_table.configurator_id');
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('step_id', 'title');
    }
}
