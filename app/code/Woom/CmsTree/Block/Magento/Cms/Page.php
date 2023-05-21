<?php

namespace Woom\CmsTree\Block\Magento\Cms;

use Magento\Cms\Block\Page as MagentoPage;
use Magento\Framework\View\Element\Context;
use Magento\Cms\Model\Page as PageModel;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\View\Page\Config;
use Magento\Framework\Registry;
use Woom\CmsTree\Api\TreeRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Woom\CmsTree\Api\Data\TreeInterface;
use Woom\CmsTree\Block\Magento\Theme\Html\Breadcrumbs;

class Page extends MagentoPage
{
    /**
     * Core registry
     *
     * @var Registry
     */
    private $registry;

    /**
     * Tree repository
     *
     * @var TreeRepositoryInterface
     */
    private $treeRepository;

    /**
     * Page constructor.
     *
     * @param Context                 $context
     * @param PageModel               $page
     * @param FilterProvider          $filterProvider
     * @param StoreManagerInterface   $storeManager
     * @param PageFactory             $pageFactory
     * @param Config                  $pageConfig
     * @param Registry                $registry
     * @param TreeRepositoryInterface $treeRepository
     */
    public function __construct(
        Context $context,
        PageModel $page,
        FilterProvider $filterProvider,
        StoreManagerInterface $storeManager,
        PageFactory $pageFactory,
        Config $pageConfig,
        Registry $registry,
        TreeRepositoryInterface $treeRepository
    ) {
        parent::__construct(
            $context,
            $page,
            $filterProvider,
            $storeManager,
            $pageFactory,
            $pageConfig
        );
        $this->treeRepository = $treeRepository;
        $this->registry = $registry;
    }

    /**
     * Prepare breadcrumbs
     *
     * @param PageModel $page
     *
     * @throws LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs(PageModel $page)
    {
        parent::_addBreadcrumbs($page);

        /** @var TreeInterface $currentTree */
        $currentTree = $this->registry->registry('current_cms_tree');
        if (!$currentTree) {
            return;
        }

        $breadcrumbs = [];
        $pathIds = explode('/', $currentTree->getPath());
        foreach ($pathIds as $treeId) {
            if ($currentTree->getId() != $treeId) {
                try {
                    $tree = $this->treeRepository->getById($treeId);
                    if ($tree->getPageId()) {
                        $breadcrumbs[] = [
                            'crumbName' => 'cms_tree_' . $tree->getId(),
                            'crumbInfo' => [
                                'label' => $tree->getTitle(),
                                'link'  => $tree->getUrl($this->_storeManager->getStore()),
                                'title' => $tree->getTitle()
                            ]
                        ];
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        /** @var Breadcrumbs $breadcrumbsBlock */
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbsBlock && !empty($breadcrumbsBlock)) {
            foreach ($breadcrumbs as $breadcrumbsItem) {
                $breadcrumbsBlock->addCrumb($breadcrumbsItem['crumbName'], $breadcrumbsItem['crumbInfo']);
            }

            $cmsCrumb = null;
            $crumbs = $breadcrumbsBlock->getCrumbs();
            if (array_key_exists('cms_page', $crumbs)) {
                $cmsCrumb = $crumbs['cms_page'];
                unset($crumbs['cms_page']);
            }
            $breadcrumbsBlock->setCrumbs($crumbs);

            if ($cmsCrumb) {
                $breadcrumbsBlock->addCrumb('cms_page', $cmsCrumb);
            }
        }
    }
}
