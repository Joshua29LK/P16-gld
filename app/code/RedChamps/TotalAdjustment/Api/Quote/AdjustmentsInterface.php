<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Api\Quote;

interface AdjustmentsInterface
{
    /**
     * Add new adjustment to quote.
     *
     * @api
     *
     * @param string $cartId
     *
     * @param mixed $adjustments
     *
     * @param bool $nonApi
     *
     * @return string $result
     */
    public function addAdjustments($cartId, $adjustments, $nonApi = false);

    /**
     * Remove adjustment from quote.
     *
     * @api
     *
     * @param string $cartId
     *
     * @param mixed $adjustments
     *
     * @param bool $nonApi
     *
     * @return string $result
     */
    public function removeAdjustments($cartId, $adjustments, $nonApi = false);

    /**
     * Edit existing adjustment from quote.
     *
     * @api
     *
     * @param string $cartId
     *
     * @param mixed $adjustments
     *
     * @param bool $nonApi
     *
     * @return string $result
     */
    public function editAdjustments($cartId, $adjustments, $nonApi = false);
}
