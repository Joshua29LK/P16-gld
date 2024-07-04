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

class Product extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $productFactory;

    /**
     * @var \Bss\Faqs\Model\Faqs
     */
    private $faqFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    private $eavAttribute;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $magentoVersion;

    /**
     * @var string
     */
    private $entityKey = 'entity_id';

    /**
     * Class constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Bss\Faqs\Model\FaqsFactory $faqFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
     * @param \Magento\Framework\App\ProductMetadataInterface $magentoVersion
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Bss\Faqs\Model\FaqsFactory $faqFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        \Magento\Framework\App\ProductMetadataInterface $magentoVersion,
        array $data = []
    ) {
        $this->productFactory = $productFactory;
        $this->faqFactory = $faqFactory;
        $this->eavAttribute = $eavAttribute;
        $this->magentoVersion = $magentoVersion;
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
        $this->setId('faqs_products');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
        if ($this->magentoVersion->getEdition() === 'Enterprise') {
            $this->entityKey = 'row_id';
        }
    }

    /**
     * Get item
     *
     * @return array|null
     */
    public function getItem()
    {
        return $this->getRequest()->getParam('faq_id');
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
            $productIds = $this->getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } elseif (!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare collection
     *
     * @return Product
     */
    public function _prepareCollection()
    {
        if ($this->getIndex() && !empty($this->getSelectedProducts())) {
            $this->setDefaultFilter(['in_faq' => 1]);
        }
        $collection = $this->productFactory->create()->getCollection()
        ->joinField(
            'name',
            'catalog_product_entity_varchar',
            'value',
            $this->entityKey . '=entity_id',
            [
                'attribute_id' => (int)$this->eavAttribute->getIdByCode('catalog_product', 'name'),
                'store_id' => 0
            ],
            'left'
        );
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
        return $this->getUrl('*/grid/productgrid', ['faq_id' => $this->getIndex()]);
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
     * @return Product
     * @throws \Exception
     */
    public function _prepareColumns()
    {
        $this->addColumn(
            'in_faq',
            [
                'type' => 'checkbox',
                'values' => $this->getSelectedProducts(),
                'index' => 'entity_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );
        $this->addColumn(
            'entity_id',
            ['header' => __('ID'), 'index' => 'entity_id', 'sortable' => true]
        );
        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);
        $this->addColumn('sku', ['header' => __('SKU'), 'index' => 'sku']);

        return parent::_prepareColumns();
    }

    /**
     * Get selected products
     *
     * @return array
     */
    public function getSelectedProducts()
    {
        $result = [];
        $faqs = $this->getRequest()->getParam('faq_id');
        if ($faqs !== null) {
            $product = $this->faqFactory->create()->load($faqs)->getProductId();
            if ($product) {
                $ids = explode(';', $product);
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
