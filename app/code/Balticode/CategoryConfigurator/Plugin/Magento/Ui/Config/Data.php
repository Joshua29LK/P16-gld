<?php

namespace Balticode\CategoryConfigurator\Plugin\Magento\Ui\Config;

use Balticode\CategoryConfigurator\Block\Adminhtml\Configurator\Edit\AddStepButton;
use Magento\Framework\App\Request\Http;
use Magento\Ui\Config\Data as BaseData;

class Data
{
    const UI_ELEMENT_CONFIGURATOR_FORM = 'balticode_categoryconfigurator_configurator_form';
    const UI_ELEMENT_STEP_INDEX = 'balticode_categoryconfigurator_step_index';
    const ROUTE_CONFIGURATOR_EDIT = 'balticode_categoryconfigurator_configurator_edit';

    /**
     * @var Http
     */
    protected $request;

    /**
     * @param Http $request
     */
    public function __construct(Http $request)
    {
        $this->request = $request;
    }

    /**
     * @param BaseData $subject
     * @param $result
     * @param null $path
     * @return mixed
     */
    public function afterGet(BaseData $subject, $result, $path = null)
    {
        $fullActionName = $this->request->getFullActionName();

        if ($fullActionName != self::ROUTE_CONFIGURATOR_EDIT) {
            return $result;
        }

        switch ($path) {
            case self::UI_ELEMENT_CONFIGURATOR_FORM:
                return $this->addStepButton($result);
            case self::UI_ELEMENT_STEP_INDEX:
                return $this->disableStepGrid($result);
        }

        return $result;
    }

    /**
     * @param $result
     * @return mixed
     */
    protected function addStepButton($result)
    {
        if (!empty($this->request->getBeforeForwardInfo('action_name'))) {
            return $result;
        }

        if (!isset($result['arguments']['data']['buttons']) ||
            isset($result['arguments']['data']['buttons']['add_step'])
        ) {
            return $result;
        }

        $result['arguments']['data']['buttons']['add_step'] = AddStepButton::class;

        return $result;
    }

    /**
     * @param array $result
     * @return array
     */
    public function disableStepGrid($result)
    {
        if (empty($this->request->getBeforeForwardInfo('action_name'))) {
            return $result;
        }

        $result['arguments']['data']['config']['componentDisabled'] = true;

        return $result;
    }
}