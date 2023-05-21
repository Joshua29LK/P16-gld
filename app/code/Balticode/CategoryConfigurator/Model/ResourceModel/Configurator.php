<?php

namespace Balticode\CategoryConfigurator\Model\ResourceModel;

use Balticode\CategoryConfigurator\Api\Data\ConfiguratorInterface;

class Configurator extends AbstractDb
{
    /**
     * Table name
     */
    const TABLE = 'balticode_categoryconfigurator_configurator';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE, ConfiguratorInterface::CONFIGURATOR_ID);
    }
}
