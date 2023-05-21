<?php

namespace Balticode\CategoryConfigurator\Block\Configurator;

use Balticode\CategoryConfigurator\Api\Data\ConfiguratorInterface;
use Balticode\CategoryConfigurator\Model\Configurator\ImageUrlProvider as ConfiguratorImageUrlProvider;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Link extends Template
{
    const CONFIGURATOR_VIEW_ACTION_PATH = 'category/configurator/view';

    /**
     * @var string
     */
    protected $_template = 'configurator/link.phtml';

    /**
     * @var ConfiguratorInterface
     */
    protected $configurator;

    /**
     * @var ConfiguratorImageUrlProvider
     */
    private $configuratorImageUrlProvider;

    /**
     * @param Context $context
     * @param ConfiguratorInterface $configurator
     * @param ConfiguratorImageUrlProvider $configuratorImageUrlProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfiguratorInterface $configurator,
        ConfiguratorImageUrlProvider $configuratorImageUrlProvider,
        array $data = []
    ) {
        $this->configurator = $configurator;
        $this->configuratorImageUrlProvider = $configuratorImageUrlProvider;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        if (!$this->configurator instanceof ConfiguratorInterface) {
            return null;
        }

        return $this->configurator->getTitle();
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        if (!$this->configurator instanceof ConfiguratorInterface) {
            return null;
        }

        return $this->configuratorImageUrlProvider->getImageUrl($this->configurator->getImageName());
    }

    /**
     * @return null|string
     */
    public function getConfiguratorUrl()
    {
        if (!$this->configurator instanceof ConfiguratorInterface) {
            return null;
        }

        return $this->getUrl(
            self::CONFIGURATOR_VIEW_ACTION_PATH,
            [
                ConfiguratorInterface::CONFIGURATOR_ID => $this->configurator->getConfiguratorId()
            ]
        );
    }
}
