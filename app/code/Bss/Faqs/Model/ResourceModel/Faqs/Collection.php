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
namespace Bss\Faqs\Model\ResourceModel\Faqs;

use Bss\Faqs\Model\ResourceModel\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Construct
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            \Bss\Faqs\Model\Faqs::class,
            \Bss\Faqs\Model\ResourceModel\Faqs::class
        );
    }

    /**
     * Add store filter
     *
     * @param int $storeId
     * @return $this
     */
    public function addStoreFilter($storeId = null)
    {
        foreach ($this->getItems() as $key => $item) {
            $storeIds = $item->getStoreArray();
            if (!in_array($storeId, $storeIds)) {
                unset($this->_items[$key]);
            }
        }
        return $this;
    }

    /**
     * Add faq sort
     *
     * @param string $sortBy
     * @return $this
     */
    public function addFaqSort($sortBy)
    {
        if ($sortBy === 'time') {
            $this->setOrder($sortBy, 'DESC');
        } elseif ($sortBy === 'title') {
            $this->setOrder($sortBy, 'ASC');
        } elseif ($sortBy === 'helpful_vote') {
            $this->setOrder($sortBy, 'DESC');
        }
        return $this;
    }

    /**
     * Add tag filter
     *
     * @param array $tagList
     * @return $this
     */
    public function addTagFilter($tagList)
    {
        foreach ($this->getItems() as $key => $item) {
            if (!array_intersect($tagList, $item->getTagArray())) {
                unset($this->_items[$key]);
            }
        }
        return $this;
    }

    /**
     * Add product filter
     *
     * @param int $productId
     * @return $this
     */
    public function addProductFilter($productId)
    {
        foreach ($this->getItems() as $key => $item) {
            if (!$item->getIsCheckAllProduct() && !in_array($productId, $item->getProductIdArray())) {
                unset($this->_items[$key]);
            }
        }
        return $this;
    }

    /**
     * Get most faq data
     *
     * @param int $number
     * @return array
     */
    public function getMostFaqData($number)
    {
        $result = [];
        foreach ($this->getItems() as $item) {
            if ($number <= 0) {
                break;
            }
            if ($item->getData('is_most_frequently')) {
                $result[] = $item->setLimitLink($this->getLimitLink())->getViewData(false);
                $number--;
            }
        }
        return $result;
    }

    /**
     * Add faq limit
     *
     * @param int $limit
     * @return $this
     */
    public function addFaqLimit($limit)
    {
        foreach ($this->getItems() as $item) {
            if ($limit <= 0) {
                unset($this->_items[$item->getId()]);
            } else {
                $limit--;
            }
        }
        return $this;
    }

    /**
     * Get all tag
     *
     * @return array
     */
    public function getAllTag()
    {
        $result = [];
        foreach ($this->getItems() as $item) {
            $result = $this->mergeResult($result, $item->getTagArray());
        }
        return $result;
    }

    /**
     * Merge result
     *
     * @param array $result
     * @param array $tagArr
     * @return array
     */
    protected function mergeResult($result, $tagArr)
    {
        return array_merge($result, $tagArr);
    }

    /**
     * Faq search
     *
     * @param string $keyword
     * @param bool $searchInTitle
     * @return $this
     */
    public function faqSearch($keyword, $searchInTitle = true)
    {
        if ($searchInTitle) {
            $field = ['title', 'answer'];
            $cond = [['like' => '%' . $keyword . '%'], ['like' => '%' . $keyword . '%']];
        } else {
            $field = ['answer'];
            $cond = ['like' => '%' . $keyword . '%'];
        }
        $this->addFieldToFilter($field, $cond);
        return $this;
    }
}
