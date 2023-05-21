<?php

namespace Woom\CmsTree\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template\Context;
use Woom\CmsTree\Model\Page\TreeRepository;
use Woom\CmsTree\Api\Data\TreeInterface;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Children extends Template implements BlockInterface
{
    /**
     * Tree repository
     *
     * @var TreeRepository
     */
    protected $treeRepository;

    /**
     * Children constructor.
     *
     * @param Context $context
     * @param TreeRepository   $treeRepository
     * @param array            $data
     */
    public function __construct(
        Context $context,
        TreeRepository $treeRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->treeRepository = $treeRepository;
    }

    /**
     * Get max levels entered in admin widget config
     *
     * @return mixed
     */
    public function getMaxLevels()
    {
        return $this->_getData('levels');
    }

    /**
     * Get "show top level" flag entered in admin widget config
     *
     * @return bool
     */
    public function isShowTopLevel()
    {
        return (bool)$this->_getData('show_top_level');
    }

    /**
     * Get tree links
     *
     * @return null|string
     * @throws NoSuchEntityException
     */
    public function getTreeLinks()
    {
        $html = null;
        $idPath = explode('/', $this->_getData('id_path'));
        if (isset($idPath[1])) {
            $treeId = $idPath[1];
            if ($treeId) {
                $tree = $this->treeRepository->getById($treeId);
                $html = $this->prepareLinks([$tree], true);
            }
        }

        return $html;
    }

    /**
     * Prepare tree links
     *
     * @param TreeInterface[] $treeChildren
     * @param bool $first
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function prepareLinks($treeChildren, $first = false)
    {
        $html = '';

        /** @var TreeInterface|PageInterface $tree */
        foreach ($treeChildren as $tree) {
            //as the first tree that can be displayed has level of 2, normalize levels
            //for easier level entry for admin users
            $normalizedLevel = $tree->getLevel() - 2;

            //if max levels is 0, show only first level of pages
            $maxLevels = $this->getMaxLevels();
            if ($maxLevels === '0') {
                $maxLevels = 1;
            }

            if ($maxLevels && $normalizedLevel > $maxLevels) {
                continue;
            }

            //if page is active or this is first iteration (first child of root node)
            if ($tree->getIsActive() || $first) {
                $href = $tree->getUrl($this->_storeManager->getStore());
                $label = $tree->getMenuLabel() ?: $tree->getTitle();
                if (!$first || ($first && $this->isShowTopLevel())) {
                    $html .= sprintf('<li><a href="%s">%s</a>', $href, $label);
                }
                $directChildren = $tree->getDirectChildren();
                if ($directChildren) {
                    $childrenHtml = $this->prepareLinks($directChildren, false);
                    if ($childrenHtml) {
                        $html .= sprintf(
                            '<ul class="cmstree-widget-submenu level%s">%s</ul>',
                            $tree->getLevel(),
                            $childrenHtml
                        );
                    }
                }
                $html .= '</li>';
            }
        }

        if ($first && $this->isShowTopLevel() && strlen($html)) {
            $html = sprintf(
                '<ul class="cmstree-widget-menu level%s">%s</ul>',
                $tree->getLevel(),
                $html
            );
        }

        return $html;
    }
}
