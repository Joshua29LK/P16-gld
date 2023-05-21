<?php

namespace MageArray\Customprice\Block\Adminhtml\Catalog\Product\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;

class Csvpricing extends \Magento\Backend\Block\Media\Uploader implements TabInterface
{
    /**
     * @var string
     */
    protected $_template = 'MageArray_Customprice::customprice/catalog/product/tab/csv.phtml';

    /**
     * @var \Magento\Backend\Model\UrlFactory
     */
    protected $_urlFactory;

    /**
     * @var $_config
     */
    protected $_config;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \MageArray\Customprice\Model\CsvpriceFactory
     */
    protected $_csvpriceFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \MageArray\Customprice\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var $_currentProduct
     */
    protected $_currentProduct;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * [__construct description]
     *
     * @param \Magento\Framework\ObjectManagerInterface           $objectManager   [description]
     * @param \Magento\Store\Model\StoreManagerInterface          $storeManager    [description]
     * @param \Magento\Backend\Block\Template\Context             $context         [description]
     * @param \Magento\Framework\File\Size                        $fileSize        [description]
     * @param \Magento\Framework\Json\EncoderInterface            $jsonEncoder     [description]
     * @param \Magento\Backend\Model\UrlFactory                   $urlFactory      [description]
     * @param \MageArray\Customprice\Model\CsvpriceFactory        $csvpriceFactory [description]
     * @param \MageArray\Customprice\Model\ResourceModel\Csvprice $csvprice        [description]
     * @param \Magento\Framework\Registry                         $coreRegistry    [description]
     * @param \MageArray\Customprice\Helper\Data                  $dataHelper      [description]
     * @param array                                               $data            [description]
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\File\Size $fileSize,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\UrlFactory $urlFactory,
        \MageArray\Customprice\Model\CsvpriceFactory $csvpriceFactory,
        \MageArray\Customprice\Model\ResourceModel\Csvprice $csvprice,
        \Magento\Framework\Registry $coreRegistry,
        \MageArray\Customprice\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_urlFactory = $urlFactory;
        $this->_csvpriceFactory = $csvpriceFactory;
        $this->csvprice = $csvprice;
        $this->_coreRegistry = $coreRegistry;
        $this->_dataHelper = $dataHelper;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $fileSize, $data);
    }

    /**
     * [getProduct description]
     *
     * @return [type] [description]
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * [getConfigJson description]
     *
     * @return [type] [description]
     */
    public function getConfigJson()
    {
        $url = $this->_urlFactory->create()->getUrl(
            'customprice/customcsv/index',
            ['_secure' => true]
        );
        $this->getConfig()->setUrl($url);
        $this->getConfig()->setParams(['form_key' => $this->getFormKey()]);
        return $this->_jsonEncoder->encode($this->getConfig()->getData());
    }

    /**
     * [getConfig description]
     *
     * @return [type] [description]
     */
    public function getConfig()
    {
        if ($this->_config === null) {
            $this->_config = new \Magento\Framework\DataObject();
        }

        return $this->_config;
    }

    /**
     * [getCsvFileData description]
     *
     * @return [type] [description]
     */
    public function getCsvFileData()
    {
        $productId = $this->_coreRegistry->registry('current_product')->getId();

        $csvpricingData = $this->csvprice->addCsvFilter($productId, 0);
        /*
        $csvpricingModel = $this->_csvpriceFactory->create();
        $csvpricingCollection = $csvpricingModel->getCollection()
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('option_sku', 0);
        $csvpricingCollection->getSelect()->limit(1);
        $csvpricingData = $csvpricingCollection->getData();
        $this->_currentProduct = $csvpricingCollection->getData(); */
        if (!empty($csvpricingData)) {
            $csvprice = $csvpricingData['csv_price'];
            if (!empty($csvprice)) {
                $jsonDecode = json_decode($csvprice, true);
                return $jsonDecode['pricesheet'];
            } else {
                return '';
            }
        } else {
            return '';
        }
        return '';
    }

    /**
     * [getColumnLabel description]
     *
     * @return [type] [description]
     */
    public function getColumnLabel()
    {
        $columnLable = $this->getProduct()->getColumnLabels();
        if ($columnLable != "") {
            return $columnLable;
        } else {
            return $this->_dataHelper
                ->getStoreConfig('customprice/general/column_label');
        }
    }

    /**
     * [getRowLabel description]
     *
     * @return [type] [description]
     */
    public function getRowLabel()
    {
        $rowLable = $this->getProduct()->getRowLabels();
        if ($rowLable != "") {
            return $rowLable;
        } else {
            return $this->_dataHelper
                ->getStoreConfig('customprice/general/row_label');
        }
    }

    /**
     * [getOptionFieldName description]
     *
     * @return [type] [description]
     */
    public function getOptionFieldName()
    {
        return $this->getProduct()->getCsvCsvLabel();
    }

    /**
     * [getProductCustomOption description]
     *
     * @return [type] [description]
     */
    public function getProductCustomOption()
    {
        $final = '';
        $id = $this->getProduct()->getId();
        $product = $this->getProduct();
        foreach ($product->getOptions() as $o) {
            if ($this->getOptionFieldName() != '' &&
                strtolower($o->getTitle()) == strtolower($this->getOptionFieldName())) {
                $final = $o;
                break;
            }
        }
        return $final;
    }

    /**
     * [getCsvPathFile description]
     *
     * @param  [type] $optId [description]
     * @return [type]        [description]
     */
    public function getCsvPathFile($optId)
    {
        $productId = $this->_coreRegistry->registry('current_product')->getId();

        $data = $this->csvprice->addCsvFilter($productId, $optId);
        // echo "<pre>"; print_R($data);exit;
        /* $csvpricingCollection = $this->_csvpriceFactory->create()->getCollection()
        ->addFieldToFilter('product_id', $productId)
        ->addFieldToFilter('option_sku', $optId)->setPageSize(1);
        $data = $csvpricingCollection->getData(); */
        if (isset($data) && !empty($data)) {
            $fileUrl = $this->_storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) .
            DIRECTORY_SEPARATOR . 'csvfiles' . $data['file_name'];
            return "<a target='_black' href='" . $fileUrl . "' >Download</a>";
        } else {
            return '';
        }
    }

    /**
     * [getCsvFileDataForView description]
     *
     * @param  [type] $optId [description]
     * @return [type]        [description]
     */
    public function getCsvFileDataForView($optId)
    {
        $productId = $this->_coreRegistry->registry('current_product')->getId();

        $data = $this->csvprice->addCsvFilter($productId, $optId);
        if (count($data) > 0) {
            $jsonDecode = json_decode($data['csv_price'], true);
            return $jsonDecode['pricesheet'];
        } else {
            return '';
        }
    }

    /**
     * [getTabLabel description]
     *
     * @return [type] [description]
     */
    public function getTabLabel()
    {
        return __('CSV Pricing');
    }

    /**
     * [getTabTitle description]
     *
     * @return [type] [description]
     */
    public function getTabTitle()
    {
        return __('CSV Pricing');
    }

    /**
     * [canShowTab description]
     *
     * @return [type] [description]
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * [isHidden description]
     *
     * @return boolean [description]
     */
    public function isHidden()
    {
        return false;
    }
}
