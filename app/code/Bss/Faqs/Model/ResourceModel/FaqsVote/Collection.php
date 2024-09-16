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
namespace Bss\Faqs\Model\ResourceModel\FaqsVote;

use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Bss\Faqs\Model\FaqsVote;

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
            \Bss\Faqs\Model\FaqsVote::class,
            \Bss\Faqs\Model\ResourceModel\FaqsVote::class
        );
    }

    /**
     * Get vote data
     *
     * @param int $customerId
     * @param int $sessionVoteId
     * @return array
     */
    public function getVoteData($customerId = null, $sessionVoteId = null)
    {
        $result = ['like' => 0, 'unlike' => 0];
        foreach ($this->getItems() as $item) {
            if ($item->getVoteValue() < 0) {
                $result['unlike']++;
            } elseif ($item->getVoteValue() > 0) {
                $result['like']++;
            }
            if ($customerId && $item->getUserId() == $customerId) {
                $result['state'] = $item->getStateAsString();
            }
            if (!$customerId &&
                $sessionVoteId &&
                $sessionVoteId == $item->getId()
            ) {
                $result['state'] = $item->getStateAsString();
            }
        }
        return $result;
    }

    /**
     * Get item by customer
     *
     * @param int $customerId
     * @return DataObject|null
     */
    public function getItemByCustomer($customerId)
    {
        return $this->getItemByColumnValue('user_id', $customerId);
    }

    /**
     * Get anonymous vote
     *
     * @return \Magento\Framework\DataObject[]
     */
    public function getAnonymousVote()
    {
        $cond = ['in' => [FaqsVote::ANONYMOUS_LIKE, FaqsVote::ANONYMOUS_UNLIKE]];
        return $this->addFieldToFilter('user_id', $cond)->getItems();
    }
}
