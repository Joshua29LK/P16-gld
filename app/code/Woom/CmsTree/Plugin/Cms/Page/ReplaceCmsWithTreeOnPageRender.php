<?php

namespace Woom\CmsTree\Plugin\Cms\Page;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Woom\CmsTree\Api\TreeRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Registry;
use Magento\Cms\Helper\Page;
use Magento\Framework\App\Action\Action;

class ReplaceCmsWithTreeOnPageRender
{
    /**
     * CMS tree model repository
     *
     * @var TreeRepositoryInterface
     */
    private $treeRepository;

    /**
     * Store manager
     *
     * @var TreeRepositoryInterface
     */
    private $storeManager;

    /**
     * Core registry
     *
     * @var Registry
     */
    private $registry;

    /**
     * ReplaceCmsWithTree constructor.
     *
     * @param TreeRepositoryInterface $treeRepository
     * @param StoreManagerInterface   $storeManager
     * @param Registry                $registry
     */
    public function __construct(
        TreeRepositoryInterface $treeRepository,
        StoreManagerInterface $storeManager,
        Registry $registry
    ) {
        $this->treeRepository = $treeRepository;
        $this->storeManager = $storeManager;
        $this->registry = $registry;
    }

    /**
     * During CMS page render, replace CMS page with page from CMS tree structure
     *
     * @param Page   $subject
     * @param Action $action
     * @param null   $pageId
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function beforePrepareResultPage(
        Page $subject,
        Action $action,
        $pageId = null
    ) {
        //do nothing, as we're already affecting this page render
        if (!$this->registry->registry('current_cms_tree')) {
            /** @var RequestInterface $request */
            $request = $action->getRequest();
            $identifiers = [
                $request->getPathInfo(),
                $request->getOriginalPathInfo()
            ];
            $storeId = $this->storeManager->getStore()->getId();

            if (!empty($identifiers)) {
                foreach ($identifiers as $identifier) {
                    $identifier = rtrim($identifier, '.html');
                    $identifier = ltrim($identifier, '/');
                    try {
                        $tree = $this->treeRepository->getByRequestUrl($identifier, $storeId);
                        if ($tree && $tree->getPageId()) {
                            $pageId = $tree->getPageId();
                            $this->registry->register('current_cms_tree', $tree);
                            break;
                        }
                    } catch (NoSuchEntityException $e) {
                        //do nothing, normal cms page will render instead
                    }
                }
            }
        } else {
            $tree = $this->registry->registry('current_cms_tree');
            $pageId = $tree->getPageId();
        }

        return [$action, $pageId];
    }
}
