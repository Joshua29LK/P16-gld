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
 * @package    Bss_Faqs
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\Faqs\Block\Adminhtml\Grid\Assign\Table;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class FaqCategory extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $faqFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $faqCategoryFactory;

    /**
     * Class constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Bss\Faqs\Model\FaqCategoryFactory $faqCategoryFactory
     * @param \Bss\Faqs\Model\FaqsFactory $faqFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Bss\Faqs\Model\FaqCategoryFactory $faqCategoryFactory,
        \Bss\Faqs\Model\FaqsFactory $faqFactory,
        array $data = []
    ) {
        $this->faqFactory = $faqFactory;
        $this->faqCategoryFactory = $faqCategoryFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Construct
     *
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('faqs_category');
        $this->setDefaultSort('faq_category_id_grid');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
    }

    /**
     * Add column filter to collection
     *
     * @param Column $column
     * @return $this|Extended
     */
    public function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_faq') {
            $cates = $this->getSelectedCategory();
            if (empty($cates)) {
                $cates = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('faq_category_id', ['in' => $cates]);
            } elseif (!empty($cates)) {
                $this->getCollection()->addFieldToFilter('faq_category_id', ['nin' => $cates]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare collection
     *
     * @return FaqCategory
     */
    public function _prepareCollection()
    {
        if ($this->getIndex() && !empty($this->getSelectedCategory())) {
            $this->setDefaultFilter(['in_faq' => 1]);
        }
        $collection = $this->faqCategoryFactory->create()->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/grid/categorygrid', ['index' => $this->getIndex()]);
    }

    /**
     * Get row url
     *
     * @param mixed $item
     * @return string|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getRowUrl($item)
    {
        return null;
    }

    /**
     * Prepare columns
     *
     * @return FaqCategory
     * @throws \Exception
     */
    public function _prepareColumns()
    {
        $this->addColumn(
            'in_faq',
            [
                'type' => 'checkbox',
                'values' => $this->getSelectedCategory(),
                'index' => 'faq_category_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );
        $this->addColumn(
            'faq_category_id_grid',
            ['header' => __('ID'), 'index' => 'faq_category_id', 'sortable' => true]
        );
        $this->addColumn('title_grid', ['header' => __('Title'), 'index' => 'title']);
        $this->addColumn('url_key_grid', ['header' => __('URL Key'), 'index' => 'url_key']);

        return parent::_prepareColumns();
    }

    /**
     * Get selected category
     *
     * @return array
     */
    public function getSelectedCategory()
    {
        $result = [];
        $faqs = $this->getIndex();
        if ($faqs !== null) {
            $cates = $this->faqFactory->create()->load($faqs)->getCategoryId();
            if ($cates) {
                $ids = explode(';', $cates);
                foreach ($ids as $value) {
                    if ($value != '' && !in_array($value, $result)) {
                        $result[] = $value;
                    }
                }
            }

        }
        return $result;
    }
}
