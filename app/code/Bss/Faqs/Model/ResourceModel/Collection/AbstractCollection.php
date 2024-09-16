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
namespace Bss\Faqs\Model\ResourceModel\Collection;

class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var null
     */
    private $limitLink = null;

    /**
     * Set limit link
     *
     * @param int $number
     * @return $this
     */
    public function setLimitLink($number)
    {
        $this->limitLink = $number;
        return $this;
    }

    /**
     * Get limit link
     *
     * @return |null
     */
    public function getLimitLink()
    {
        return $this->limitLink;
    }

    /**
     * Remove link id
     *
     * @param int $id
     * @return AbstractCollection
     */
    public function removeLinkId($id)
    {
        foreach ($this->getItems() as $key => $item) {
            $linkIds = $item->getLinkId();
            $isUpdate = false;
            foreach ($linkIds as $linkKey => $faqId) {
                if ($faqId == $id) {
                    unset($linkIds[$linkKey]);
                    $isUpdate = true;
                    break;
                }
            }
            if ($isUpdate) {
                $item->setLinkId($linkIds)->setIsUpdating(true);
            } else {
                unset($this->_items[$key]);
            }
        }
        return $this->save();
    }

    /**
     * Update link id
     *
     * @param int $id
     * @return AbstractCollection
     */
    public function updateLinkId($id)
    {
        foreach ($this->getItems() as $key => $item) {
            $linkIds = $item->getLinkId();
            if (!in_array($id, $linkIds)) {
                $linkIds[] = $id;
                sort($linkIds);
                $item->setLinkId($linkIds)->setIsUpdating(true);
            } else {
                unset($this->_items[$key]);
            }
        }
        return $this->save();
    }

    /**
     * Get view data
     *
     * @param bool $hasLink
     * @return array
     */
    public function getViewData($hasLink = true)
    {
        $result = [];
        foreach ($this->getItems() as $item) {
            $this->beforeGetViewItem($item);
            $result[] = $item->getViewData($hasLink);
        }
        return $result;
    }

    /**
     * Before get view item
     *
     * @param mixed $item
     */
    protected function beforeGetViewItem($item)
    {
        $item->setLimitLink($this->getLimitLink());
    }

    /**
     * Add empty filter
     *
     * @return $this
     */
    public function addEmptyFilter()
    {
        foreach ($this->getItems() as $key => $item) {
            if (empty($item->getLinkId())) {
                unset($this->_items[$key]);
            }
        }
        return $this;
    }

    /**
     * Add id filter
     *
     * @param array $ids
     * @return $this
     */
    public function addIdFilter($ids)
    {
        foreach ($this->getItems() as $key => $item) {
            if (!in_array($item->getId(), $ids)) {
                unset($this->_items[$key]);
            }
        }
        return $this;
    }
}
