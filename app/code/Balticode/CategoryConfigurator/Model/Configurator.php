<?php

namespace Balticode\CategoryConfigurator\Model;

use Balticode\CategoryConfigurator\Api\Data\ConfiguratorInterface;
use Balticode\CategoryConfigurator\Api\Data\StepInterface;
use Balticode\CategoryConfigurator\Model\ResourceModel\Step\Collection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Balticode\CategoryConfigurator\Model\ResourceModel\Configurator as ResourceConfigurator;
use Balticode\CategoryConfigurator\Model\ResourceModel\Step\CollectionFactory as StepCollectionFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class Configurator extends AbstractModel implements ConfiguratorInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'balticode_categoryconfigurator_configurator';

    /**
     * @var StepCollectionFactory
     */
    protected $stepCollectionFactory;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param StepCollectionFactory $stepCollectionFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StepCollectionFactory $stepCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->stepCollectionFactory = $stepCollectionFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceConfigurator::class);
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
     * @return ConfiguratorInterface
     */
    public function setConfiguratorId($configuratorId)
    {
        return $this->setData(self::CONFIGURATOR_ID, $configuratorId);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return (string)$this->getData(self::TITLE);
    }

    /**
     * @param string $title
     * @return ConfiguratorInterface
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
        return (string)$this->getData(self::DESCRIPTION);
    }

    /**
     * @param string $description
     * @return ConfiguratorInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @return bool
     */
    public function getEnable()
    {
        return (bool)$this->getData(self::ENABLE);
    }

    /**
     * @param string $enable
     * @return ConfiguratorInterface
     */
    public function setEnable($enable)
    {
        return $this->setData(self::ENABLE, $enable);
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->getData(self::CATEGORY_ID);
    }

    /**
     * @param int $categoryId
     * @return ConfiguratorInterface
     */
    public function setCategoryId($categoryId)
    {
        return $this->setData(self::CATEGORY_ID, $categoryId);
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->getData(self::IMAGE_NAME);
    }

    /**
     * @param string $imageName
     * @return ConfiguratorInterface
     */
    public function setImageName($imageName)
    {
        return $this->setData(self::IMAGE_NAME, $imageName);
    }
}
