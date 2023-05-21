<?php

namespace Woom\CmsTree\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\PageRepository;
use Woom\CmsTree\Model\Page\TreeFactory;
use Woom\CmsTree\Model\Page\TreeRepository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\View\Result\PageFactory as ResultPageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\LayoutFactory;
use Magento\Backend\Model\Auth\Session;
use Psr\Log\LoggerInterface;
use Magento\Cms\Controller\Adminhtml\Page\PostDataProcessor;
use Magento\Framework\App\Request\DataPersistorInterface;
use Woom\CmsTree\Api\Data\TreeInterface;
use Magento\Framework\Exception\NoSuchEntityException;

abstract class Page extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Cms::page';

    /**
     * Registry
     *
     * @var Registry
     */
    protected $registry;

    /**
     * Page factory
     *
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * Page repository
     *
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * Tree factory
     *
     * @var TreeFactory
     */
    protected $treeFactory;

    /**
     * Tree repository
     *
     * @var TreeRepository
     */
    protected $treeRepository;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ForwardFactory
     */
    protected $forwardFactory;

    /**
     * Redirect factory
     *
     * @var RedirectFactory
     */
    protected $redirectFactory;

    /**
     * Result page factory
     *
     * @var ResultPageFactory
     */
    protected $resultPageFactory;

    /**
     * Json factory
     *
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Layout factory
     *
     * @var LayoutFactory
     */
    protected $layoutFactory;

    /**
     * Session
     *
     * @var Session
     */
    protected $session;

    /**
     * Logger
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Data processor
     *
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * Data persistor
     *
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * Request parameters
     *
     * @var array
     */
    protected $params = [];

    /**
     * Page constructor.
     *
     * @param Context                $context
     * @param Registry               $registry
     * @param PageFactory            $pageFactory
     * @param PageRepository         $pageRepository
     * @param TreeFactory            $treeFactory
     * @param TreeRepository         $treeRepository
     * @param StoreManagerInterface  $storeManager
     * @param ForwardFactory         $forwardFactory
     * @param ResultPageFactory      $resultPageFactory
     * @param JsonFactory            $resultJsonFactory
     * @param LayoutFactory          $layoutFactory
     * @param Session                $session
     * @param LoggerInterface        $logger
     * @param PostDataProcessor      $dataProcessor
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        Registry $registry,
        PageFactory $pageFactory,
        PageRepository $pageRepository,
        TreeFactory $treeFactory,
        TreeRepository $treeRepository,
        StoreManagerInterface $storeManager,
        ForwardFactory $forwardFactory,
        ResultPageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        LayoutFactory $layoutFactory,
        Session $session,
        LoggerInterface $logger,
        PostDataProcessor $dataProcessor,
        DataPersistorInterface $dataPersistor
    ) {
        $this->registry = $registry;
        $this->pageFactory = $pageFactory;
        $this->pageRepository = $pageRepository;
        $this->treeFactory = $treeFactory;
        $this->treeRepository = $treeRepository;
        $this->storeManager = $storeManager;
        $this->forwardFactory = $forwardFactory;
        $this->redirectFactory = $context->getResultRedirectFactory();
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
        $this->session = $session;
        $this->logger = $logger;
        $this->dataProcessor = $dataProcessor;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Filter blank parameters from request, so we don't have false-positive trees
     * e.g. with tree id of "null" would usually be converted to (int)null = 0
     * which is alias for root trees
     *
     * @return array
     */
    protected function getParams()
    {
        if (!$this->params) {
            $this->params = array_filter(
                $this->getRequest()->getParams(),
                function ($value) {
                    if (is_array($value)) {
                        return array_filter($value, 'strlen');
                    } else {
                        return strlen($value);
                    }
                }
            );
        };

        return $this->params;
    }

    /**
     * Get params from filtered parameter list
     *
     * @param string $key
     * @param mixed  $defaultValue
     *
     * @return mixed|null
     */
    protected function getParam($key, $defaultValue = null)
    {
        $this->getParams();
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }

        return $defaultValue;
    }

    /**
     * Init page and tree models by provided page identifier and store
     *
     * @param int $pageId
     * @param int $storeId
     * @param int $treeId
     *
     * @return void
     */
    protected function initPageAndTree($pageId, $storeId, $treeId = null)
    {
        //init page part
        $page = $this->pageFactory->create();
        $tree = $this->treeFactory->create();

        //if this isn't a root tree, try to init page
        if ($pageId !== null) {
            try {
                $page = $this->pageRepository->getById($pageId);
                $tree = $this->treeRepository->getByPageId($pageId);
            } catch (NoSuchEntityException $e) {
                //do nothing
                //if page doesn't exist, we have a empty page object init
                //if tree doesn't exist, we'll get root tree after
            }
        }

        if ($treeId !== null) {
            try {
                $tree = $this->treeRepository->getById($treeId);
                $page = $this->pageRepository->getById($tree->getPageId());
            } catch (NoSuchEntityException $e) {
                //do nothing
                //if page doesn't exist, we have a empty page object init
                //if tree doesn't exist, we'll get root tree after
            }
        }

        //if tree by page id doesn't exist, load root tree
        if ($pageId !== null && !$page->getId() && !$tree->getId()) {
            try {
                $rootId = TreeInterface::TREE_ROOT_ID;
                if ($storeId !== null) {
                    $rootId = $this->storeManager->getStore($storeId)->getRootCmsTreeId();
                }
                $tree = $this->treeRepository->getById($rootId);
            } catch (NoSuchEntityException $e) {
                //do nothing
                //this should never happen (store should always have root tree id)
            }
        }

        $this->registry->register('cms_page', $page);
        $this->registry->register('current_page', $page);
        $this->registry->register('tree', $tree);
        $this->registry->register('current_tree', $tree);
    }

    /**
     * Init tree model by given identifier
     *
     * @param int $treeId
     *
     * @return void
     */
    protected function initOnlyTree($treeId)
    {
        //init page part
        $tree = $this->treeFactory->create();

        try {
            $tree = $this->treeRepository->getById($treeId);
        } catch (NoSuchEntityException $e) {
            //do nothing
            //if page doesn't exist, we have a empty page object init
            //if tree doesn't exist, we'll get root tree after
        }

        $this->registry->register('tree', $tree);
        $this->registry->register('current_tree', $tree);
    }

    /**
     * Get current page from registry
     *
     * @return mixed
     */
    protected function getCurrentPage()
    {
        return $this->registry->registry('current_page');
    }

    /**
     * Get current tree from registry
     *
     * @return mixed
     */
    protected function getCurrentTree()
    {
        return $this->registry->registry('current_tree');
    }

    /**
     * Return ids of root pages as array
     *
     * @return array
     */
    public function getRootIds()
    {
        $ids = [TreeInterface::TREE_ROOT_ID];
        foreach ($this->storeManager->getStores(true) as $store) {
            $ids[] = $store->getRootCmsTreeId();
        }

        return $ids;
    }
}
