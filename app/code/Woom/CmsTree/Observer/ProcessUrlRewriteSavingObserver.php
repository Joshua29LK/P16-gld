<?php

namespace Woom\CmsTree\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\CmsUrlRewrite\Model\CmsPageUrlRewriteGenerator;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Woom\CmsTree\Model\Page\Tree;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\Framework\Exception\LocalizedException;
use Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException;

class ProcessUrlRewriteSavingObserver implements ObserverInterface
{
    /**
     * @var CmsPageUrlRewriteGenerator
     */
    private $cmsPageUrlRewriteGenerator;

    /**
     * @var UrlPersistInterface
     */
    private $urlPersist;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @param CmsPageUrlRewriteGenerator $cmsPageUrlRewriteGenerator
     * @param UrlPersistInterface $urlPersist
     * @param PageRepositoryInterface $pageRepository
     */
    public function __construct(
        CmsPageUrlRewriteGenerator $cmsPageUrlRewriteGenerator,
        UrlPersistInterface $urlPersist,
        PageRepositoryInterface $pageRepository
    ) {
        $this->cmsPageUrlRewriteGenerator = $cmsPageUrlRewriteGenerator;
        $this->urlPersist = $urlPersist;
        $this->pageRepository = $pageRepository;
    }

    /**
     * Generate urls for UrlRewrite and save it in storage
     *
     * @param EventObserver $observer
     *
     * @return void
     * @throws LocalizedException
     * @throws UrlAlreadyExistsException
     */
    public function execute(EventObserver $observer)
    {
        /** @var $tree Tree */
        $tree = $observer->getEvent()->getObject();
        
        if (!$tree->getPageId()) return;

        $cmsPage = $this->pageRepository->getById($tree->getPageId());

        if ($cmsPage->dataHasChangedFor('identifier') || $cmsPage->dataHasChangedFor('store_id')) {
            $urls = $this->cmsPageUrlRewriteGenerator->generate($cmsPage);

            $this->urlPersist->deleteByData([
                UrlRewrite::ENTITY_ID => $cmsPage->getPageId(),
                UrlRewrite::ENTITY_TYPE => CmsPageUrlRewriteGenerator::ENTITY_TYPE,
            ]);
            $this->urlPersist->replace($urls);
        }
    }
}
