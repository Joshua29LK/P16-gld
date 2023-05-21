<?php

namespace Woom\CmsTree\Block\Adminhtml\Page\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Save button
     *
     * @return array
     */
    public function getButtonData()
    {
        $tree = $this->getTree();

        //allow save if tree doesn't exist (new page)
        //disallow save if tree is root
        if (!$tree || !$tree->getId() || !in_array($tree->getId(), $this->getRootIds())) {
            return [
                'label' => __('Save Page'),
                'class' => 'save primary',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'save']],
                    'form-role' => 'save',
                ],
                'sort_order' => 30,
            ];
        }

        return [];
    }
}
