<?php

namespace Woom\CmsTree\Block\Adminhtml\Page\Widget;

use Woom\CmsTree\Block\Adminhtml\Page\TreeBlock;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Widget\Block\Adminhtml\Widget\Chooser as WidgetChooser;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Exception\LocalizedException;

class Chooser extends TreeBlock
{
    /**
     * Selected trees
     *
     * @var array
     */
    protected $selectedTrees = [];

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'Woom_CmsTree::page/widget/tree.phtml';

    /**
     * Set selected trees
     *
     * @param array $selectedTrees
     *
     * @return $this
     */
    public function setSelectedTrees($selectedTrees)
    {
        $this->selectedTrees = $selectedTrees;

        return $this;
    }

    /**
     * Get selected trees
     *
     * @return array
     */
    public function getSelectedTrees()
    {
        return $this->selectedTrees;
    }

    /**
     * Prepare chooser element HTML
     *
     * @param AbstractElement $element Form Element
     *
     * @return AbstractElement
     * @throws LocalizedException
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $uniqId = $this->mathRandom->getUniqueHash($element->getId());
        $sourceUrl = $this->getUrl(
            'cmstree/page_widget/chooser',
            [
                'uniq_id' => $uniqId,
                'store' => $this->_request->getParam('store')
            ]
        );

        $chooser = $this->getLayout()->createBlock(
            WidgetChooser::class
        )->setElement(
            $element
        )->setConfig(
            $this->getConfig()
        )->setFieldsetId(
            $this->getFieldsetId()
        )->setSourceUrl(
            $sourceUrl
        )->setUniqId(
            $uniqId
        );

        if ($element->getValue()) {
            $value = explode('/', $element->getValue());
            $treeId = false;
            if (isset($value[0]) && isset($value[1]) && $value[0] == 'tree') {
                $treeId = $value[1];
            }
            if ($treeId) {
                $label = $this->treeFactory->create()->load($treeId)->getTitle();
                $chooser->setLabel($label);
            }
        }

        $element->setData('after_element_html', $chooser->toHtml());

        return $element;
    }

    /**
     * Tree node onClick listener js function
     *
     * @return string
     */
    public function getNodeClickListener()
    {
        if ($this->getData('node_click_listener')) {
            return $this->getData('node_click_listener');
        }
        $chooserJsObject = $this->getId();
        $jsFunction = 'function (node, e) {
                    ' . $chooserJsObject . '.setElementValue("tree/" + node.attributes.id);
                    ' . $chooserJsObject . '.setElementLabel(node.text);
                    ' . $chooserJsObject . '.close();
                }';

        return $jsFunction;
    }

    /**
     * Get JSON of a tree node or an associative array
     *
     * @param Node|array $node
     * @param int        $level
     *
     * @return array
     */
    protected function getNodeJson($node, $level = 0)
    {
        $item = parent::getNodeJson($node, $level);
        if (in_array($node->getId(), $this->getSelectedTrees())) {
            $item['checked'] = true;
        }

        return $item;
    }

    /**
     * Tree JSON source URL
     *
     * @param bool|null $expanded
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getLoadTreeUrl($expanded = null)
    {
        return $this->getUrl(
            'cmstree/page_widget/treeJson',
            [
                '_current' => true,
                'uniq_id'  => $this->getId(),
                'store'    => $this->_request->getParam('store')
            ]
        );
    }
}
