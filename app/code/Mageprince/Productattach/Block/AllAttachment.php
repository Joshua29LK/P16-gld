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

use Mageprince\Productattach\Model\Config\DefaultConfig;

class AllAttachment extends \Mageprince\Productattach\Block\Attachment
{
    /**
     * Get all attachments
     * @return \Mageprince\Productattach\Model\ResourceModel\Productattach\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAllAttachment()
    {
        $collection = $this->getCollection();

        if ($this->getFileType()) {
            $collection->addFieldToFilter('file_ext', ['in' => $this->getFileType()]);
        }

        if ($this->getAttachmentId()) {
            $collection->addFieldToFilter('productattach_id', ['in' => $this->getAttachmentId()]);
        }

        if ($this->isApplyCustomerFilter()) {
            $collection->addFieldToFilter(
                'customer_group',
                [
                    ['null' => true],
                    ['finset' => $this->getCustomerId()]
                ]
            );
        }

        if ($this->isApplyStoreFilter()) {
            $collection->addFieldToFilter(
                'store',
                [
                    ['eq' => 0],
                    ['finset' => $this->dataHelper->getStoreId()]
                ]
            );
        }

        if ($this->getCount()) {
            $collection->setPageSize($this->getCount());
        }

        return $collection;
    }

    /**
     * Check block value
     * @param $val
     * @return bool
     */
    public function checkConfigValue($val)
    {
        if ($val != null && $val == 1) {
            return true;
        }
    }

    /**
     * Can show attachment size
     * @return bool
     */
    public function isEnableAttachmentSize()
    {
        return $this->checkConfigValue($this->getShowSize());
    }

    /**
     * Get attachment type
     * @return bool
     */
    public function getAttachmentView()
    {
        if ($this->getAttachmentView() == DefaultConfig::ATTACHMENT_VIEW_TYPE_LIST) {
            return DefaultConfig::ATTACHMENT_VIEW_TYPE_LIST;
        }
        return DefaultConfig::ATTACHMENT_VIEW_TYPE_TABLE;
    }

    /**
     * Can show icon
     * @return bool
     */
    public function isEnableIcon()
    {
        return $this->checkConfigValue($this->getShowIcon());
    }

    /**
     * Can show label
     * @return bool
     */
    public function isEnableLabel()
    {
        return $this->checkConfigValue($this->getShowLabel());
    }

    /**
     * Can show description
     * @return bool
     */
    public function isEnableDescription()
    {
        return $this->checkConfigValue($this->getShowDescription());
    }

    /**
     * Can show file type
     * @return bool
     */
    public function isEnableFileType()
    {
        return $this->checkConfigValue($this->getShowFiletype());
    }

    /**
     * Can show download
     * @return bool
     */
    public function isEnableDownload()
    {
        return $this->checkConfigValue($this->getShowDownload());
    }

    /**
     * Apply customer filter
     * @return bool
     */
    public function isApplyCustomerFilter()
    {
        return $this->checkConfigValue($this->getApplyCustomerFilter());
    }

    /**
     * Apply store filter
     * @return bool
     */
    public function isApplyStoreFilter()
    {
        return $this->checkConfigValue($this->getApplyStoreFilter());
    }
}
