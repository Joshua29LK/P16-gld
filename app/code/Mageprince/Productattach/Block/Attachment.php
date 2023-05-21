<?php

/**
 * MagePrince
 * Copyright (C) 2020 Mageprince <info@mageprince.com>
 *
 * @package Mageprince_Productattach
 * @copyright Copyright (c) 2020 Mageprince (http://www.mageprince.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MagePrince <info@mageprince.com>
 */

namespace Mageprince\Productattach\Block;

use Magento\Customer\Model\Context as CustomerModelContext;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Mageprince\Productattach\Helper\Data as DataHelper;
use Mageprince\Productattach\Model\Config\DefaultConfig;
use Mageprince\Productattach\Model\ResourceModel\Fileicon\CollectionFactory as FileIconCollectionFactory;
use Mageprince\Productattach\Model\ResourceModel\Product as ProductRelationModel;
use Mageprince\Productattach\Model\ResourceModel\Productattach\Collection as AttachmentCollection;
use Mageprince\Productattach\Model\ResourceModel\Productattach\CollectionFactory as AttachmentCollectionFactory;

class Attachment extends Template
{
    /**
     * Product Attachment collection
     *
     * @var AttachmentCollection
     */
    protected $productattachCollection = null;

    /**
     * @var AttachmentCollectionFactory
     */
    protected $productattachCollectionFactory;

    /**
     * @var FileIconCollectionFactory
     */
    protected $fileiconCollectionFactory;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var ProductRelationModel
     */
    protected $productRelationModel;

    /**
     * Attachment constructor.
     *
     * @param Template\Context $context
     * @param CustomerSession $customerSession
     * @param AttachmentCollectionFactory $productattachCollectionFactory
     * @param FileIconCollectionFactory $fileiconCollectionFactory
     * @param ProductRelationModel $productRelationModel
     * @param DataHelper $dataHelper
     * @param Registry $registry
     * @param HttpContext $httpContext
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CustomerSession $customerSession,
        AttachmentCollectionFactory $productattachCollectionFactory,
        FileIconCollectionFactory $fileiconCollectionFactory,
        ProductRelationModel $productRelationModel,
        DataHelper $dataHelper,
        Registry $registry,
        HttpContext $httpContext,
        array $data = []
    ) {
        $this->customerSession =$customerSession;
        $this->productattachCollectionFactory = $productattachCollectionFactory;
        $this->fileiconCollectionFactory = $fileiconCollectionFactory;
        $this->productRelationModel = $productRelationModel;
        $this->dataHelper = $dataHelper;
        $this->registry = $registry;
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
    }

    /**
     * Check module is enable or not
     *
     * @return bool
     */
    public function isEnable()
    {
        return $this->getConfig(DefaultConfig::XML_PATH_ENABLE);
    }

    /**
     * Can show attachment size
     *
     * @return bool
     */
    public function isEnableListViewSize()
    {
        return $this->isListViewColumnVisible(DefaultConfig::SIZE_COLUMN);
    }

    /**
     * Can show attachment size
     *
     * @return bool
     */
    public function isEnableTableViewSize()
    {
        return $this->isTableViewColumnVisible(DefaultConfig::SIZE_COLUMN);
    }

    /**
     * Get attachment type
     *
     * @return bool
     */
    public function getAttachmentView()
    {
        return $this->getConfig(DefaultConfig::XML_PATH_ATTACHMENT_VIEW);
    }

    /**
     * Can show attachment header
     *
     * @return bool
     */
    public function isEnableTableViewHeader()
    {
        return $this->isTableViewColumnVisible(DefaultConfig::HEADER_COLUMN);
    }

    /**
     * Can show list view icon
     *
     * @return bool
     */
    public function isEnableListViewIcon()
    {
        return $this->isListViewColumnVisible(DefaultConfig::ICON_COLUMN);
    }

    /**
     * Can show table view icon
     *
     * @return bool
     */
    public function isEnableTableViewIcon()
    {
        return $this->isTableViewColumnVisible(DefaultConfig::ICON_COLUMN);
    }

    /**
     * Can show table view type
     *
     * @return bool
     */
    public function isEnableTableViewType()
    {
        return $this->isTableViewColumnVisible(DefaultConfig::TYPE_COLUMN);
    }

    /**
     * Can show list view label
     *
     * @return bool
     */
    public function isEnableListViewLabel()
    {
        return $this->isListViewColumnVisible(DefaultConfig::LABEL_COLUMN);
    }

    /**
     * Can show table view label
     *
     * @return bool
     */
    public function isEnableTableViewLabel()
    {
        return $this->isTableViewColumnVisible(DefaultConfig::LABEL_COLUMN);
    }

    /**
     * Can show table view description
     *
     * @return bool
     */
    public function isEnableTableViewDescription()
    {
        return $this->isTableViewColumnVisible(DefaultConfig::DESCRIPTION_COLUMN);
    }

    /**
     * Can show download
     *
     * @return bool
     */
    public function isEnableTableViewDownload()
    {
        return $this->isTableViewColumnVisible(DefaultConfig::DOWNLOAD_COLUMN);
    }

    /**
     * Check if file exists
     *
     * @param $attachment
     * @return bool|\Magento\Framework\Exception\FileSystemException
     */
    public function fileExists($attachment)
    {
        try {
            $this->getFileSize($attachment);
            return true;
        } catch (\Exception $e) {
            return new \Magento\Framework\Exception\FileSystemException(
                __('File not found')
            );
        }
    }

    /**
     * Get attachments collection
     *
     * @return AttachmentCollection
     */
    public function getCollection()
    {
        $collection = $this->productattachCollectionFactory->create();
        return $collection;
    }

    /**
     * Filter attachments collection by product Id
     *
     * @param int $productId
     * @return AttachmentCollection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttachment($productId)
    {
        $collection = $this->getCollection();
        $collection->addFieldToFilter('active', 1);
        $collection->addFieldToFilter(
            'customer_group',
            [
                ['null' => true],
                ['finset' => $this->getCustomerId()]
            ]
        );
        $collection->addFieldToFilter(
            'store',
            [
                ['eq' => 0],
                ['finset' => $this->dataHelper->getStoreId()]
            ]
        );
        $collection = $this->productRelationModel->filterAttachmentsByProductId($collection, $productId);
        return $collection;
    }

    /**
     * Get attachment url by attachment
     *
     * @param $attachment
     * @return string
     * @throws NoSuchEntityException
     */
    public function getAttachmentUrl($attachment)
    {
        $url = $this->dataHelper->getBaseUrl().$attachment;
        return $url;
    }

    /**
     * Get current product id
     *
     * @return number
     */
    public function getCurrentId()
    {
        $product = $this->registry->registry('current_product');
        return $product->getId();
    }

    /**
     * Get current customer id
     *
     * @return number
     */
    public function getCustomerId()
    {
        $isLoggedIn = $this->httpContext->getValue(CustomerModelContext::CONTEXT_AUTH);
        if (!$isLoggedIn) {
            return 0;
        }

        $customerId = $this->customerSession->getCustomer()->getGroupId();
        return $customerId;
    }

    /**
     * Get file icon image
     *
     * @param string $fileExt
     * @return string
     * @throws NoSuchEntityException
     */
    public function getFileIcon($fileExt)
    {
        $fileExt = \strtolower($fileExt);
        $iconExt = $this->getIconExt($fileExt);
        if ($iconExt) {
            $mediaUrl = $this->dataHelper->getMediaUrl();
            $iconImage = $mediaUrl.'fileicon/tmp/icon/'.$iconExt;
        } elseif (in_array($fileExt, $this->dataHelper->getDefaultIcons())) {
            $iconImage = $this->getViewFileUrl('Mageprince_Productattach::images/'.$fileExt.'.svg');
        } else {
            $iconImage = $this->getViewFileUrl(DefaultConfig::UNKNOWN_ICON_PATH);
        }
        return $iconImage;
    }

    /**
     * Get icon ext name
     *
     * @return string
     */
    public function getIconExt($fileExt)
    {
        $iconCollection = $this->fileiconCollectionFactory->create();
        $iconCollection->addFieldToFilter('icon_ext', $fileExt);
        $icon = $iconCollection->getFirstItem()->getIconImage();
        return $icon;
    }

    /**
     * Get link icon image
     *
     * @return string
     */
    public function getLinkIcon()
    {
        $iconImage = $this->getViewFileUrl(DefaultConfig::LINK_ICON_PATH);
        return $iconImage;
    }

    /**
     * Get file size by attachment
     *
     * @param $attachment
     * @return string
     */
    public function getFileSize($attachment)
    {
        $attachmentPath = DefaultConfig::MEDIA_PATH.$attachment;
        $fileSize = $this->dataHelper->getFileSize($attachmentPath);
        return $fileSize;
    }

    /**
     * Get config value
     *
     * @param $config
     * @return mixed
     */
    public function getConfig($config)
    {
        return $this->dataHelper->scopeConfig->getValue(
            $config,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get tab name
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabName()
    {
        $tabName = __($this->getConfig(DefaultConfig::XML_PATH_TABNAME));
        return $tabName;
    }

    /**
     * Get list view columns
     *
     * @return mixed
     */
    public function getListViewColumns()
    {
        return $this->getConfig(DefaultConfig::XML_PATH_LIST_VIEW_COLUMNS);
    }

    /**
     * Check is enable list view columns
     *
     * @param $column
     * @return bool
     */
    public function isListViewColumnVisible($column)
    {
        $listViewColumns = $this->getConfig(DefaultConfig::XML_PATH_LIST_VIEW_COLUMNS);
        $listViewColumnsArray = explode(',', $listViewColumns);
        if (in_array($column, $listViewColumnsArray)) {
            return true;
        }
        return false;
    }

    /**
     * Check is enable table view columns
     *
     * @param $column
     * @return bool
     */
    public function isTableViewColumnVisible($column)
    {
        $listViewColumns = $this->getConfig(DefaultConfig::XML_PATH_TABLE_VIEW_COLUMNS);
        $listViewColumnsArray = explode(',', $listViewColumns);
        if (in_array($column, $listViewColumnsArray)) {
            return true;
        }
        return false;
    }
}
