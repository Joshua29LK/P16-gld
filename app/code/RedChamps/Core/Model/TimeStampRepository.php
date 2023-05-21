<?php
namespace RedChamps\Core\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use RedChamps\Core\Api\TimeStampRepositoryInterface;
use RedChamps\Core\Model\ResourceModel\TimeStamp as TimeStampResource;

class TimeStampRepository implements TimeStampRepositoryInterface
{
    /**
     * @var TimeStamp
     */
    protected $resource;

    /**
     * @var TimeStampFactory
     */
    protected $timeStampFactory;

    /**
     * @param TimeStamp $resource
     * @param TimeStampFactory $timeStampFactory
     */
    public function __construct(
        TimeStampResource $resource,
        TimeStampFactory $timeStampFactory
    ) {
        $this->resource = $resource;
        $this->timeStampFactory = $timeStampFactory;
    }

    /**
     * Save Quote Request
     *
     * @param TimeStamp $history
     * @return TimeStamp
     * @throws CouldNotSaveException
     */
    public function save(TimeStamp $history)
    {
        try {
            $this->resource->save($history);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the timestamp item: %1',
                    $exception->getMessage()
                )
            );
        }

        return $history;
    }

    /**
     * Load Additional quote request by given id
     *
     * @param string $id
     * @return TimeStamp
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        /** @var TimeStamp $history */
        $history = $this->timeStampFactory->create();
        $this->resource->load($history, $id);
        if (!$history->getId()) {
            throw new NoSuchEntityException(__('Timestamp with id "%1" does not exist.', $id));
        }

        return $history;
    }

    /**
     * Get empty Quote Request
     *
     * @return TimeStamp
     */
    public function getEmptyInstance()
    {
        return $this->timeStampFactory->create();
    }

    /**
     * Retrieve timestamp data.
     *
     * @param $type
     * @return TimeStamp
     * @throws NoSuchEntityException
     */
    public function getByType($type)
    {
        $timeStamp = $this->getEmptyInstance();
        $this->resource->load($timeStamp, $type, "type");
        if (!$timeStamp->getId()) {
            throw new NoSuchEntityException(__('Timestamp with type "%1" does not exist.', $type));
        }
        return $timeStamp;
    }
}
