<?php

namespace Woom\CmsTree\Block\Adminhtml\Page\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Exception\NoSuchEntityException;

class PreviewButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Preview button
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getButtonData()
    {
        $tree = $this->getTree();
        $page = $this->getPage();

        //if page and tree exist, we can get request url for tree
        if ($page && $tree && $page->getId() && $tree->getRequestUrl()) {
            return [
                'label'      => __('Preview'),
                'class'      => 'preview',
                'on_click'   => sprintf("location.href = '%s';", $this->getPreviewUrl($tree, $page)),
                'sort_order' => 30,
            ];
        }

        return [];
    }

    /**
     * Get preview url for page
     *
     * @param $tree
     * @param $page
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getPreviewUrl($tree, $page)
    {
        if (empty($page->getStoreId())) {
            $stores = $this->storeManager->getStores(false, true);
            $storeId = current($stores)->getId();
            $storeCode = key($stores);
        } else {
            $storeId = current($page->getStoreId());
            $storeCode = $this->storeManager->getStore($storeId)->getCode();
        }

        //if admin store, use default store view for preview
        if ($storeId == Store::DEFAULT_STORE_ID) {
            $storeId = $this->storeManager->getDefaultStoreView()->getId();
            $storeCode = $this->storeManager->getStore($storeId)->getCode();
        }

        $href = $this->frontendUrlBuilder->getUrl(
            $tree->getRequestUrl(),
            $storeId,
            $storeCode
        );

        return $href;
    }
}
