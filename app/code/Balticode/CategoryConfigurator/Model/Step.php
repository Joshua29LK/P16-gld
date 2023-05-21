<?php

namespace Balticode\CategoryConfigurator\Model;

use Balticode\CategoryConfigurator\Api\Data\StepInterface;
use Balticode\CategoryConfigurator\Model\ResourceModel\Step as StepResource;
use Magento\Framework\Model\AbstractModel;

class Step extends AbstractModel implements StepInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'balticode_categoryconfigurator_step';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(StepResource::class);
    }

    /**
     * @return string
     */
    public function getStepId()
    {
        return $this->getData(self::STEP_ID);
    }

    /**
     * @param string $stepId
     * @return StepInterface
     */
    public function setStepId($stepId)
    {
        return $this->setData(self::STEP_ID, $stepId);
    }

    /**
     * @return string
     */
    public function getConfiguratorId()
    {
        return $this->getData(self::CONFIGURATOR_ID);
    }

    /**
     * @param string $configuratorId
     * @return StepInterface
     */
    public function setConfiguratorId($configuratorId)
    {
        return $this->setData(self::CONFIGURATOR_ID, $configuratorId);
    }

    /**
     * @return bool
     */
    public function getEnable()
    {
        return (bool)$this->getData(self::ENABLE);
    }

    /**
     * @param bool $enable
     * @return StepInterface
     */
    public function setEnable($enable)
    {
        return $this->setData(self::ENABLE, $enable);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @param string $title
     * @return StepInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @param string $description
     * @return StepInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @return array
     */
    public function getCategoryId()
    {
        return $this->getData(self::CATEGORY_ID);
    }

    /**
     * @param $id
     * @return StepInterface
     */
    public function setCategoryId($id)
    {
        return $this->setData(self::CATEGORY_ID, $id);
    }

    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->getData(self::INFO);
    }

    /**
     * @param $info
     * @return StepInterface
     */
    public function setInfo($info)
    {
        return $this->setData(self::INFO, $info);
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * @param $order
     * @return StepInterface
     */
    public function setSortOrder($order)
    {
        return $this->setData(self::SORT_ORDER, $order);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @param $type
     * @return StepInterface
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * @return array
     */
    public function getParentId()
    {
        return $this->getData(self::PARENT_ID);
    }

    public function getSecondParentId()
    {
        return $this->getData(self::SECOND_PARENT_ID);
    }

    /**
     * @param $id
     * @return StepInterface
     */
    public function setParentId($id)
    {
        return $this->setData(self::PARENT_ID, $id);
    }

    public function setSecondParentId($id)
    {
        return $this->setData(self::SECOND_PARENT_ID, $id);
    }

    /**
     * @return bool
     */
    public function isFullWidth()
    {
        return $this->getData(self::FULL_WIDTH);
    }

    /**
     * @param bool $isFullWidth
     * @return StepInterface
     */
    public function setFullWidthFlag($isFullWidth)
    {
        return $this->setData(self::FULL_WIDTH, $isFullWidth);
    }
}
