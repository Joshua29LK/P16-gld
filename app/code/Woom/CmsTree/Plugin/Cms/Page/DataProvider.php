<?php

namespace Woom\CmsTree\Plugin\Cms\Page;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
use Woom\CmsTree\Model\Page\TreeRepository;
use Magento\Cms\Model\ResourceModel\Page\Collection;
use Magento\Cms\Model\Page\DataProvider as PageDataProvider;
use Magento\Framework\Exception\NoSuchEntityException;

class DataProvider
{
    /**
     * Context
     *
     * @var ContextInterface
     */
    private $context;

    /**
     * CMS page collection
     *
     * @var Collection
     */
    private $collection;

    /**
     * Tree model repository
     *
     * @var TreeRepository
     */
    private $treeRepository;

    /**
     * Array of loaded data in data provider
     *
     * @var array
     */
    protected $loadedData;

    /**
     * DataProvider constructor.
     *
     * @param ContextInterface  $context
     * @param CollectionFactory $pageCollectionFactory
     * @param TreeRepository    $treeRepository
     */
    public function __construct(
        ContextInterface $context,
        CollectionFactory $pageCollectionFactory,
        TreeRepository $treeRepository
    ) {
        $this->context = $context;
        $this->collection = $pageCollectionFactory->create();
        $this->treeRepository = $treeRepository;
    }

    /**
     * Add path and parent (CMS tree parameters) to CMS page form
     *
     * @param PageDataProvider $subject
     * @param                  $result
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function afterGetData(PageDataProvider $subject, $result)
    {
        $resultData = [];

        if ($result) {
            $storeId = $this->context->getRequestParam('store');
            /** @var $page PageInterface */
            foreach ($result as $pageId => $pageData) {
                try {
                    //only provide data for pages that have corresponding trees
                    if ($pageId) {
                        $tree = $this->treeRepository->getByPageId($pageId);
                        $pageData['store'] = $storeId;
                        $pageData['store_id'] = $storeId;
                        $pageData['path'] = $tree->getPath();
                        $pageData['parent'] = $tree->getParentId();
                        $pageData['level'] = $tree->getLevel();
                        $pageData['is_in_menu'] = $tree->isInMenu();
                        $pageData['menu_label'] = $tree->getMenuLabel();
                        $pageData['menu_add_type'] = $tree->getMenuAddType();
                        $pageData['menu_add_category_id'] = $tree->getMenuAddCategoryId();
                        $resultData[$pageId] = $pageData;
                    } else {
                        $resultData[null] = $this->initPageWithoutId();
                    }
                } catch (NoSuchEntityException $e) {
                }
            }
        }

        $resultData[null] = $this->initPageWithoutId();

        return $resultData;
    }

    /**
     * Init page that does not have id (root tree)
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function initPageWithoutId()
    {
        //init empty page store
        $storeId = $this->context->getRequestParam('store');
        $pageData['store'] = $storeId;
        $pageData['store_id'] = $storeId;

        //init empty page parent
        $parentId = $this->context->getRequestParam('parent');
        $pageData['parent'] = $parentId;

        //init empty page path
        if ($parentId) {
            $tree = $this->treeRepository->getById($parentId);
            $pageData['path'] = $tree->getPath();
        }

        return $pageData;
    }
}
