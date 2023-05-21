<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_EasycatalogimgNSideBarCustomize
 * @author     Extension Team
 * @copyright  Copyright (c) 2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\EasycatalogimgNSideBarCustomize\Block;

use Magento\Catalog\Model\Category;

class Sidebar extends \Sebwite\Sidebar\Block\Sidebar
{
    /**
     * @var \Bss\EasycatalogimgNSideBarCustomize\Block\SubcategoriesList
     */
    protected $subcategories;

    /**
     * Sidebar constructor.
     *
     * @param Template\Context $context
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param Product\Collection $productCollectionFactory
     * @param \Magento\Catalog\Helper\Output $helper
     * @param \Sebwite\Sidebar\Helper\Data $dataHelper
     * @param SubcategoriesList $subcategories
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollectionFactory,
        \Magento\Catalog\Helper\Output $helper,
        \Sebwite\Sidebar\Helper\Data $dataHelper,
        \Bss\EasycatalogimgNSideBarCustomize\Block\SubcategoriesList $subcategories,
        $data = []
    ) {
        $this->subcategories = $subcategories;
        parent::__construct(
            $context,
            $categoryHelper,
            $registry,
            $categoryFlatState,
            $categoryFactory,
            $scopeConfig,
            $productCollectionFactory,
            $helper,
            $dataHelper,
            $data
        );
    }

    /**
     * Get possible product url
     *
     * Active when category have only one product
     *
     * @param Category $category
     * @return string
     */
    public function getProductUrl($category)
    {
        return $this->subcategories->getProductUrl($category);
    }

    /**
     * @inheritDoc
     */
    public function getChildCategoryView($category, $html = '', $level = 1)
    {
        // Check if category has children
        if ( $category->hasChildren() )
        {
            $childCategories = $this->getSubcategories($category);
            try {
                $count = count($childCategories);
            } catch (\Exception $exception) {
                $count = 0;
            }

            if ( $count > 0 )
            {
                $html .= '<ul class="o-list o-list--unstyled">';

                // Loop through children categories
                foreach ( $childCategories as $childCategory )
                {

                    $html .= '<li class="level' . $level . ($this->isActive($childCategory) ? ' active' : '') . '">';
                    $html .= '<a href="' . $this->getProductUrl($childCategory) . '" title="' . $childCategory->getName() . '" class="' . ($this->isActive($childCategory) ? 'is-active' : '') . '">' . $childCategory->getName() . '</a>';

                    if ( $childCategory->hasChildren() )
                    {
                        if ( $this->isActive($childCategory) )
                        {
                            $html .= '<span class="expanded"><i class="fa fa-minus"></i></span>';
                        }
                        else
                        {
                            $html .= '<span class="expand"><i class="fa fa-plus"></i></span>';
                        }
                    }

                    if ( $childCategory->hasChildren() )
                    {
                        $html .= $this->getChildCategoryView($childCategory, '', ($level + 1));
                    }

                    $html .= '</li>';
                }
                $html .= '</ul>';
            }
        }

        return $html;
    }

    /**
     * Retrieve subcategories
     * DEPRECATED
     *
     * @param $category
     *
     * @return array
     */
     
    public function getSubcategories($category)
    {
        if ( $this->categoryFlatConfig->isFlatEnabled() && $category->getUseFlatResource() )
        {
            return (array)$category->getChildrenNodes();
        }

        if ($category instanceof \Magento\Framework\Data\Tree\Node)
        {
            $category = $this->_categoryFactory->create()->load($category->getId());
        }

        return $category->getChildrenCategories();
    }

    /**
     * Get current category
     *
     * @param \Magento\Catalog\Model\Category $category
     *
     * @return Category
     */
    public function isActive($category)
    {
        $activeCategory = $this->_coreRegistry->registry('current_category');
        $activeProduct  = $this->_coreRegistry->registry('current_product');

        if ( !$activeCategory )
        {

            // Check if we're on a product page
            if ( $activeProduct !== null )
            {
                return in_array($category->getId(), $activeProduct->getCategoryIds());
            }

            return false;
        }

        // Check if this is the active category
        if ( $this->categoryFlatConfig->isFlatEnabled() && $category->getUseFlatResource() AND
            $category->getId() == $activeCategory->getId()
        )
        {
            return true;
        }

        if ($category instanceof \Magento\Framework\Data\Tree\Node && $category->getId() == $activeCategory->getId())
        {
            return true;
        }

        if ($category instanceof \Magento\Framework\Data\Tree\Node)
        {
            $category = $this->_categoryFactory->create()->load($category->getId());
        }

        // Check if a subcategory of this category is active
        $childrenIds = $category->getAllChildren(true);
        if ( !is_null($childrenIds) AND in_array($activeCategory->getId(), $childrenIds) )
        {
            return true;
        }

        // Fallback - If Flat categories is not enabled the active category does not give an id
        return (($category->getName() == $activeCategory->getName()) ? true : false);
    }
}
