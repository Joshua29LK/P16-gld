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

namespace Bss\Faqs\Model;

use Magento\Framework\Exception\LocalizedException;

class AbstractModel extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var null
     */
    protected $linkCollection = null;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filter;

    /**
     * AbstractModel constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Filter\FilterManager $filter
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Filter\FilterManager $filter,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->filter = $filter;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Before save
     *
     * @return \Magento\Framework\Model\AbstractModel|void
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        parent::beforeSave();
        $this->validateUrl();
        if ($this->getId() && !$this->getIsUpdating()) {
            $oldLink = $this->getLinkIdOldvalue();
            $newLink = $this->getLinkId();
            $deleteLink = array_diff($oldLink, $newLink);
            if (isset($deleteLink)) {
                $this->getLinkCollection($deleteLink)->removeLinkId($this->getId());
            }
            $updateLink = array_diff($newLink, $oldLink);
            if (isset($updateLink)) {
                $this->getLinkCollection($updateLink)->updateLinkId($this->getId());
            }
        }
    }

    /**
     * Validate url
     *
     * @return bool
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function validateUrl()
    {
        $id = $this->getId();
        $urlKey = $this->filter->translitUrl($this->getUrlKey());
        if ($id) {
            if ($urlKey) {
                $result = $this->getResourceCollection()->addFieldToFilter('url_key', $urlKey);
                $result->addFieldToFilter($this->getIdFieldName(), ['neq' => $id]);
                if (!empty($result->getAllIds())) {
                    throw new LocalizedException(__("URL '%1' already exist", $this->getUrlKey()));
                }
            } else {
                $urlKey = $this->createUrlKey();
                if (!$urlKey) {
                    throw new LocalizedException(__("Can not save Url Key '%1'", $this->getUrlKey()));
                }
                $this->setUrlKey($urlKey);
            }
        } else {
            if ($urlKey) {
                $result = $this->getResourceCollection()->addFieldToFilter('url_key', $urlKey);
                if (!empty($result->getAllIds())) {
                    throw new LocalizedException(__("URL '%1' already exist", $this->getUrlKey()));
                }
            } else {
                $urlKey = $this->createUrlKey();
                if (!$urlKey) {
                    throw new LocalizedException(__("Can not save Url Key '%1'", $this->getUrlKey()));
                }
                $this->setUrlKey($urlKey);
            }
        }
        return true;
    }

    /**
     * Create url key
     *
     * @return bool|string
     * @throws LocalizedException
     */
    protected function createUrlKey()
    {
        if ($this->filter->translitUrl($this->getTitle())) {
            $title = $this->filter->translitUrl($this->getTitle());
        } else {
            $title = "";
        }
        $urlKey = urlencode($title);
        $result = $this->checkIssetUrlKey($urlKey);
        if ($result) {
            return $urlKey;
        } else {
            for ($i = 1; $i <= 20; $i++) {
                $urlKey = $urlKey . '-' . $i;
                $result = $this->checkIssetUrlKey($urlKey);
                if ($result) {
                    return $urlKey;
                } else {
                    for ($j = 1; $j <= 20; $j++) {
                        $urlKey = $urlKey . '-' . $i . '-' . $j;
                        $result = $this->checkIssetUrlKey($urlKey);
                        if ($result) {
                            return $urlKey;
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * Check isset url key
     *
     * @param string $urlKey
     * @return bool
     * @throws LocalizedException
     */
    protected function checkIssetUrlKey($urlKey)
    {
        $result = $this->getResourceCollection()->addFieldToFilter('url_key', $urlKey);
        if (empty($result->getAllIds())) {
            return true;
        }
        return false;
    }

    /**
     * Before delete
     *
     * @return \Magento\Framework\Model\AbstractModel|void
     * @throws LocalizedException
     */
    public function beforeDelete()
    {
        parent::beforeDelete();
        if ($this->getId()) {
            $this->getLinkCollection()->removeLinkId($this->getId());
        }
    }
}
