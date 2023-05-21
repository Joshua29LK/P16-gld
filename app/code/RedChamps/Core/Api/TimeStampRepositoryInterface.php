<?php
namespace RedChamps\Core\Api;

use Magento\Framework\Exception\LocalizedException;
use RedChamps\Core\Model\TimeStamp;

interface TimeStampRepositoryInterface
{
    /**
     * @param $id
     * @return TimeStamp
     */
    public function getById($id);

    /**
     * Retrieve list by Quote Request ID.
     *
     * @param $id
     * @return TimeStamp
     * @throws LocalizedException
     */
    public function getByType($id);

    /**
     * Save quote request
     *
     * @param TimeStamp $timeStamp
     * @return TimeStamp
     */
    public function save(TimeStamp $timeStamp);

    /**
     * Get empty instance
     *
     * @return TimeStamp
     */
    public function getEmptyInstance();
}
