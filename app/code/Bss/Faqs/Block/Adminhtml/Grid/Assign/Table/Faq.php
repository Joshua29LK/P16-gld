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

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class Faq extends \Magento\Backend\Block\Widget\Grid\Extended
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
     * AssignProducts constructor.
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
        $this->setId('faqs_grid');
        $this->setDefaultSort('faq_id_grid');
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
        if ($column->getId() == 'in_' . $this->getType()) {
            $idList = $this->getSelectedFaq();
            if (empty($idList)) {
                $idList = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('faq_id', ['in' => $idList]);
            } elseif (!empty($idList)) {
                $this->getCollection()->addFieldToFilter('faq_id', ['nin' => $idList]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare collection
     *
     * @return Faq
     */
    public function _prepareCollection()
    {
        if ($this->getIndex() && !empty($this->getSelectedFaq())) {
            $this->setDefaultFilter(['in_' . $this->getType() => 1]);
        }
        $collection = $this->faqFactory->create()->getCollection();
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
        return $this->getUrl('*/grid/faqgrid', ['type' => $this->getType(), 'index' => $this->getIndex()]);
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
     * @return Faq
     * @throws \Exception
     */
    public function _prepareColumns()
    {
        $this->addColumn(
            'in_' . $this->getType(),
            [
                'type' => 'checkbox',
                'values' => $this->getSelectedFaq(),
                'index' => 'faq_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );
        $this->addColumn(
            'faq_id_grid',
            ['header' => __('ID'), 'index' => 'faq_id', 'sortable' => true]
        );
        $this->addColumn('title_grid', ['header' => __('Title'), 'index' => 'title']);
        $this->addColumn('url_key_grid', ['header' => __('URL Key'), 'index' => 'url_key']);

        return parent::_prepareColumns();
    }

    /**
     * Get selected faq
     *
     * @return array
     */
    public function getSelectedFaq()
    {
        $result = [];
        if ($this->getType() == 'category') {
            $result = $this->returnSelectedCate($result);
        } elseif ($this->getType() == 'related') {
            $result = $this->returnSelectRelated($result);
        }
        return $result;
    }

    /**
     * Return selected Cate
     *
     * @param array $result
     * @return array
     */
    protected function returnSelectedCate($result)
    {
        $cateId = $this->getIndex();
        if ($cateId !== null) {
            $faqs = $this->faqCategoryFactory->create()->load($cateId)->getFaqId();
            if ($faqs) {
                $ids = explode(';', $faqs);
                foreach ($ids as $value) {
                    if ($value != '' && !in_array($value, $result)) {
                        $result[] = $value;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Return select related
     *
     * @param array $result
     * @return array
     */
    protected function returnSelectRelated($result)
    {
        $faqs = $this->getIndex();
        if ($faqs !== null) {
            $relatedFaqs = $this->faqFactory->create()->load($faqs)->getRelatedFaqId();
            if ($relatedFaqs) {
                $ids = explode(';', $relatedFaqs);
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
