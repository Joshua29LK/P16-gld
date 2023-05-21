<?php

namespace Balticode\CategoryConfigurator\Block\Configurator;

use Balticode\CategoryConfigurator\Api\ConfiguratorRepositoryInterface;
use Balticode\CategoryConfigurator\Api\Data\ConfiguratorInterface;
use Balticode\CategoryConfigurator\Helper\SearchCriteria\Configurator as ConfiguratorSearchCriteria;
use Balticode\CategoryConfigurator\Model\Category\RelatedCategoryProvider;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Index extends Template
{
    const CONFIGURATOR_INDEX_ACTION_PATH = 'category/configurator/index';

    /**
     * @var ConfiguratorRepositoryInterface
     */
    protected $configuratorRepository;

    /**
     * @var ConfiguratorSearchCriteria
     */
    protected $configuratorSearchCriteria;

    /**
     * @var LinkFactory
     */
    protected $linkFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var RelatedCategoryProvider
     */
    protected $relatedCategoryProvider;

    /**
     * @param Context $context
     * @param ConfiguratorRepositoryInterface $configuratorRepository
     * @param ConfiguratorSearchCriteria $configuratorSearchCriteria
     * @param LinkFactory $linkFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param RelatedCategoryProvider $relatedCategoryProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfiguratorRepositoryInterface $configuratorRepository,
        ConfiguratorSearchCriteria $configuratorSearchCriteria,
        LinkFactory $linkFactory,
        CategoryRepositoryInterface $categoryRepository,
        RelatedCategoryProvider $relatedCategoryProvider,
        array $data = []
    ) {
        $this->configuratorRepository = $configuratorRepository;
        $this->configuratorSearchCriteria = $configuratorSearchCriteria;
        $this->linkFactory = $linkFactory;
        $this->categoryRepository = $categoryRepository;
        $this->relatedCategoryProvider = $relatedCategoryProvider;

        parent::__construct($context, $data);
    }

    /**
     * @return Link[]
     */
    public function getLinkBlocks()
    {
        $blocks = [];

        foreach ($this->getConfigurators() as $configurator) {
            $blocks[] = $this->linkFactory->create(['configurator' => $configurator]);
        }

        return $blocks;
    }

    /**
     * @return ConfiguratorInterface[]|array
     */
    protected function getConfigurators()
    {
        $searchCriteria = $this->getConfiguratorListSearchCriteria();

        try {
            return $this->configuratorRepository->getList($searchCriteria)->getItems();
        } catch (LocalizedException $e) {
            return [];
        }
    }

    /**
     * @return SearchCriteria
     */
    protected function getConfiguratorListSearchCriteria()
    {
        $categoryId = (int)$this->getRequest()->getParam(ConfiguratorInterface::CATEGORY_ID);

        if (!$categoryId) {
            return $this->configuratorSearchCriteria->getEnabledConfiguratorsSearchCriteria();
        }

        $category = $this->getCategory($categoryId);
        $categoryIds = $this->relatedCategoryProvider->getRelatedCategoryIds($category);

        if (!$categoryIds) {
            return $this->configuratorSearchCriteria->getEnabledConfiguratorsSearchCriteria();
        }

        return $this->configuratorSearchCriteria->getEnabledConfiguratorsByCategoryIdsSearchCriteria($categoryIds);
    }

    /**
     * @param int $categoryId
     * @return CategoryInterface|null
     */
    protected function getCategory($categoryId)
    {
        if (!$categoryId) {
            return null;
        }

        try {
            return $this->categoryRepository->get($categoryId);
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}
