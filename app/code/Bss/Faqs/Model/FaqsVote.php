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

use Magento\Framework\Model\AbstractModel;

class FaqsVote extends AbstractModel
{
    const ANONYMOUS_LIKE = 0;
    const ANONYMOUS_UNLIKE = -2;
    const VOTE_STATE_UNLIKE = 'unlike_state';
    const VOTE_STATE_LIKE = 'like_state';

    /**
     * Construct
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(\Bss\Faqs\Model\ResourceModel\FaqsVote::class);
    }

    /**
     * Set new vote
     *
     * @param int $type
     * @throws \Exception
     */
    public function setNewVote($type)
    {
        if (!$this->getId() || $this->getVoteValue() * $type < 0) {
            $this->setVoteValue($type)->save();
        } else {
            $this->delete();
        }
    }

    /**
     * Add vote
     *
     * @param int $type
     */
    public function addVote($type)
    {
        $currentVote = $this->getData('vote_value', 0);
        if ($type * $this->getVoteValue() >= 0) {
            $this->setVoteValue($currentVote + $type)->save();
        } else {
            $this->unVote($type * (-1));
        }
        return $this;
    }

    /**
     * Un vote
     *
     * @param int $type
     * @throws \Exception
     */
    public function unVote($type)
    {
        if ($this->getVoteValue() * $type <= 1) {
            $this->delete();
        } else {
            $this->setVoteValue($this->getVoteValue() - $type)->save();
        }
    }

    /**
     * Get state as string
     *
     * @return string
     */
    public function getStateAsString()
    {
        $value = $this->getVoteValue();
        if ($value < 0) {
            return self::VOTE_STATE_UNLIKE;
        } elseif ($value > 0) {
            return self::VOTE_STATE_LIKE;
        } else {
            return '';
        }
    }
}
