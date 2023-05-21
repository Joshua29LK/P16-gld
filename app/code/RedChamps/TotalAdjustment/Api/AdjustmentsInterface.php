<?php
/**
 * Created by RedChamps.
 * User: rav
 * Date: 2019-01-04
 * Time: 16:54
 */
namespace RedChamps\TotalAdjustment\Api;

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