<?php

namespace Woom\CmsTree\Block\Adminhtml\Page\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Woom\CmsTree\Api\Data\TreeInterface;

class GenericButton
{
    /**
     * Registry
     *
     * @var Registry
     */
    protected $registry;

    /**
     * Frontend url builder
     *
     * @var UrlBuilder
     */
    protected $frontendUrlBuilder;

    /**
     * Url builder
     *
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * GenericButton constructor.
     *
     * @param Context    $context
     * @param Registry   $registry
     * @param UrlBuilder $frontendUrlBuilder
     */
    public function __construct(
        Context $context,
        Registry $registry,
        UrlBuilder $frontendUrlBuilder
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->storeManager = $context->getStoreManager();
        $this->frontendUrlBuilder = $frontendUrlBuilder;
        $this->registry = $registry;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array  $params
     *
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }

    /**
     * Get page from registry
     *
     * @return mixed
     */
    protected function getPage()
    {
        return $this->registry->registry('current_page');
    }

    /**
     * Get tree from registry
     *
     * @return mixed
     */
    protected function getTree()
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
