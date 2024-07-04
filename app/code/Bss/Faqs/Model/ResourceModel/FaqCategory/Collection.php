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
namespace Bss\Faqs\Model\ResourceModel\FaqCategory;

use Bss\Faqs\Model\ResourceModel\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $faqSort = 'time';

    /**
     * Construct
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            \Bss\Faqs\Model\FaqCategory::class,
            \Bss\Faqs\Model\ResourceModel\FaqCategory::class
        );
    }

    /**
     * Set faq sort
     *
     * @param string $sort
     * @return $this
     */
    public function setFaqSort($sort)
    {
        $this->faqSort = $sort;
        return $this;
    }

    /**
     * Get Faq Sort
     *
     * @return string
     */
    public function getFaqSort()
    {
        return $this->faqSort;
    }

    /**
     * Add visible filter
     *
     * @return $this
     */
    public function addVisibleFilter()
    {
        foreach ($this->getItems() as $key => $item) {
            if (!$item->getData('show_in_mainpage')) {
                unset($this->_items[$key]);
            }
        }
        return $this;
    }

    /**
     * Before get view item
     *
     * @param mixed $item
     */
    protected function beforeGetViewItem($item)
    {
        $item->setFaqSort($this->getFaqSort());
        parent::beforeGetViewItem($item);
    }
}
