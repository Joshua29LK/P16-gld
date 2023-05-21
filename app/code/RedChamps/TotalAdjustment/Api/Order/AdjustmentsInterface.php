<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Api\Order;

interface AdjustmentsInterface
{
    /**
     * Add new adjustment to order.
     *
     * @api
     *
     * @param int $orderId
     *
     * @param mixed $adjustments
     *
     * @param bool $nonApi
     *
     * @return string $result
     */
    public function addAdjustments($orderId, $adjustments, $nonApi = false);

    /**
     * Remove adjustment from order.
     *
     * @api
     *
     * @param int $orderId
     *
     * @param mixed $adjustments
     *
     * @param bool $nonApi
     *
     * @return string $result
     */
    public function removeAdjustments($orderId, $adjustments, $nonApi = false);

    /**
     * Edit existing adjustment from order.
     *
     * @api
     *
     * @param int $orderId
     *
     * @param mixed $adjustments
     *
     * @param bool $nonApi
     *
     * @return string $result
     */
    public function editAdjustments($orderId, $adjustments, $nonApi = false);
}
