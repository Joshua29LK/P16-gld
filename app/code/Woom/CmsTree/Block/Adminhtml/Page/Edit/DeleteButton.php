<?php

namespace Woom\CmsTree\Block\Adminhtml\Page\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Delete button
     *
     * @return array
     */
    public function getButtonData()
    {
        $tree = $this->getTree();

        //allow deletion only if tree exists and is not root
        if (!$tree || !$tree->getId() || !in_array($tree->getId(), $this->getRootIds())) {
            return [
                'id'         => 'delete',
                'label'      => __('Delete Page'),
                'on_click'   => "pageDelete('" . $this->getDeleteUrl() . "')",
                'class'      => 'delete',
                'sort_order' => 10
            ];
        }

        return [];
    }

    /**
     * Get delete URL for button
     *
     * @param array $args
     *
     * @return string
     */
    public function getDeleteUrl(array $args = [])
    {
        $params = array_merge($this->getDefaultUrlParams(), $args);

        return $this->getUrl('cmstree/*/delete', $params);
    }

    /**
     * Get default URL parameters for button
     *
     * @return array
     */
    protected function getDefaultUrlParams()
    {
        return ['_current' => true, '_query' => ['isAjax' => null]];
    }
}
