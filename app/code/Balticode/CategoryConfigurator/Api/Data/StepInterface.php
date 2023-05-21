<?php

namespace Balticode\CategoryConfigurator\Api\Data;

interface StepInterface
{
    const STEP_ID = 'step_id';
    const CONFIGURATOR_ID = 'configurator_id';
    const ENABLE = 'enable';
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    const INFO = 'info';
    const CATEGORY_ID = 'category_id';
    const PARENT_ID = 'parent_id';
    const SECOND_PARENT_ID = 'second_parent_id';
    const SORT_ORDER = 'sort_order';
    const TYPE = 'type';
    const FULL_WIDTH = 'full_width';

    /**
     * @return int|null
     */
    public function getStepId();

    /**
     * @param int $stepId
     * @return StepInterface
     */
    public function setStepId($stepId);

    /**
     * @return int|null
     */
    public function getConfiguratorId();

    /**
     * @param int $configuratorId
     * @return ConfiguratorInterface
     */
    public function setConfiguratorId($configuratorId);

    /**
     * @return bool
     */
    public function getEnable();

    /**
     * @param bool $enable
     * @return StepInterface
     */
    public function setEnable($enable);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     * @return StepInterface
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return StepInterface
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getInfo();

    /**
     * @param $info
     * @return StepInterface
     */
    public function setInfo($info);

    /**
     * @return int
     */
    public function getCategoryId();

    /**
     * @param int $id
     * @return StepInterface
     */
    public function setCategoryId($id);

    /**
     * @return int
     */
    public function getParentId();

    /**
     * @param int $id
     * @return StepInterface
     */
    public function setParentId($id);

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $order
     * @return StepInterface
     */
    public function setSortOrder($order);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param $type
     * @return StepInterface
     */
    public function setType($type);

    /**
     * @return bool
     */
    public function isFullWidth();

    /**
     * @param bool $isFullWidth
     * @return StepInterface
     */
    public function setFullWidthFlag($isFullWidth);
}
