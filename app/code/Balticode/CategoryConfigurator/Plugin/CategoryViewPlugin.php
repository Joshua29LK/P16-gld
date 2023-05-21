<?php

namespace Balticode\CategoryConfigurator\Plugin;

use Balticode\CategoryConfigurator\Api\ConfiguratorRepositoryInterface;
use Balticode\CategoryConfigurator\Api\Data\ConfiguratorInterface;
use Balticode\CategoryConfigurator\Block\Configurator\Index;
use Balticode\CategoryConfigurator\Block\Configurator\Link;
use Balticode\CategoryConfigurator\Helper\SearchCriteria\Configurator as ConfiguratorSearchCriteria;
use Balticode\CategoryConfigurator\Model\Category\RelatedCategoryProvider;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Controller\Category\View;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;

class CategoryViewPlugin
{
    /**
     * @var ConfiguratorRepositoryInterface
     */
    protected $configuratorRepository;

    /**
     * @var ConfiguratorSearchCriteria
     */
    protected $configuratorSearchCriteria;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var RelatedCategoryProvider
     */
    protected $relatedCategoryProvider;

    /**
     * @param ConfiguratorRepositoryInterface $configuratorRepository
     * @param ConfiguratorSearchCriteria $configuratorSearchCriteria
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ResultFactory $resultFactory
     * @param UrlInterface $url
     * @param RelatedCategoryProvider $relatedCategoryProvider
     */
    public function __construct(
        ConfiguratorRepositoryInterface $configuratorRepository,
        ConfiguratorSearchCriteria $configuratorSearchCriteria,
        CategoryRepositoryInterface $categoryRepository,
        ResultFactory $resultFactory,
        UrlInterface $url,
        RelatedCategoryProvider $relatedCategoryProvider
    ) {
        $this->configuratorRepository = $configuratorRepository;
        $this->configuratorSearchCriteria = $configuratorSearchCriteria;
        $this->categoryRepository = $categoryRepository;
        $this->resultFactory = $resultFactory;
        $this->url = $url;
        $this->relatedCategoryProvider = $relatedCategoryProvider;
    }

    /**
     * @param View $subject
     * @param callable $proceed
     * @return ResultInterface
     */
    public function aroundExecute(View $subject, callable $proceed)
    {
        $categoryId = (int) $subject->getRequest()->getParam('id', false);

        if (!$categoryId || !$category = $this->getCategory($categoryId)) {
            return $proceed();
        }

        $categoryIds = $this->relatedCategoryProvider->getRelatedCategoryIds($category);
        $configurators = $this->getConfiguratorsByCategoryIds($categoryIds);

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if (!count($configurators)) {
            return $proceed();
        }

        $firstConfigurator = reset($configurators);

        if (count($configurators) == 1 && $configuratorId = $firstConfigurator->getConfiguratorId()) {
            return $this->handleSingleConfiguratorRedirect($resultRedirect, $configuratorId);
        }

        if (count($configurators) > 1) {
            return $this->handleConfiguratorIndexRedirect($resultRedirect, $categoryId);
        }

        return $proceed();
    }

    /**
     * @param string $categoryIds
     * @return ConfiguratorInterface[]|array
     */
    protected function getConfiguratorsByCategoryIds($categoryIds)
    {
        $searchCriteria = $this->configuratorSearchCriteria->getEnabledConfiguratorsByCategoryIdsSearchCriteria(
            $categoryIds
        );

        try {
            return $this->configuratorRepository->getList($searchCriteria)->getItems();
        } catch (LocalizedException $e) {
            return [];
        }
    }

    /**
     * @param int $categoryId
     * @return CategoryInterface|null
     */
    protected function getCategory($categoryId)
    {
        try {
            return $this->categoryRepository->get($categoryId);
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * @param ResultInterface $resultRedirect
     * @param int $configuratorId
     * @return ResultInterface
     */
    protected function handleSingleConfiguratorRedirect($resultRedirect, $configuratorId)
    {
        $configuratorUrl = $this->url->getUrl(
            Link::CONFIGURATOR_VIEW_ACTION_PATH,
            [ConfiguratorInterface::CONFIGURATOR_ID => $configuratorId]
        );

        $resultRedirect->setUrl($configuratorUrl);

        return $resultRedirect;
    }

    /**
     * @param ResultInterface $resultRedirect
     * @param int $categoryId
     * @return ResultInterface
     */
    protected function handleConfiguratorIndexRedirect($resultRedirect, $categoryId)
    {
        $configuratorIndexUrl = $this->url->getUrl(
            Index::CONFIGURATOR_INDEX_ACTION_PATH,
            [ConfiguratorInterface::CATEGORY_ID => $categoryId]
        );

        $resultRedirect->setUrl($configuratorIndexUrl);

        return $resultRedirect;
    }
}