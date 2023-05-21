<?php
/**
 * Created by RedChamps.
 * User: rav
 * Date: 2019-01-04
 * Time: 16:57
 */
namespace RedChamps\TotalAdjustment\Model;

use RedChamps\TotalAdjustment\Api\AdjustmentsInterface;
use RedChamps\TotalAdjustment\Model\AdjustmentModifier\Actions;

class AdjustmentsModifier implements AdjustmentsInterface
{
    protected $addActionProcessor;

    protected $removeActionProcessor;

    protected $editActionProcessor;

    /**
     * AdjustmentsModifier constructor.
     * @param Actions\Add $addAction
     * @param Actions\Remove $removeAction
     * @param Actions\Edit $editAction
     */
    public function __construct(
        Actions\Add $addAction,
        Actions\Remove $removeAction,
        Actions\Edit $editAction
    )
    {
        $this->addActionProcessor = $addAction;
        $this->removeActionProcessor = $removeAction;
        $this->editActionProcessor = $editAction;
    }

    /**
     * @param int $orderId
     * @param mixed $adjustments
     * @param bool $nonApi
     * @return false|\Magento\Framework\Phrase|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addAdjustments($orderId, $adjustments, $nonApi = false)
    {
        return $this->addActionProcessor->execute($orderId, $adjustments, $nonApi);
    }

    /**
     * @param int $orderId
     * @param mixed $adjustmentTitles
     * @param bool $nonApi
     * @return false|\Magento\Framework\Phrase|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function removeAdjustments($orderId, $adjustmentTitles, $nonApi = false)
    {
        return $this->removeActionProcessor->execute($orderId, $adjustmentTitles, $nonApi);
    }

    /**
     * @param int $orderId
     * @param mixed $adjustments
     * @param bool $nonApi
     * @return false|\Magento\Framework\Phrase|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function editAdjustments($orderId, $adjustments, $nonApi = false)
    {
        return $this->editActionProcessor->execute($orderId, $adjustments, $nonApi);
    }
}