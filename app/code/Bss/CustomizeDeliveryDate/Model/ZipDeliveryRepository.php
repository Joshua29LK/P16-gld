<?php
namespace Bss\CustomizeDeliveryDate\Model;

use Bss\CustomizeDeliveryDate\Model\ZipDeliveryFactory;
use Bss\CustomizeDeliveryDate\Model\ResourceModel\ZipDelivery as ZipDeliveryResource;
use Bss\CustomizeDeliveryDate\Model\ResourceModel\ZipDelivery\CollectionFactory;

class ZipDeliveryRepository
{
    /**
     * @var ZipDeliveryFactory
     */
    protected $zipDeliveryFactory;

    /**
     * @var ZipDeliveryResource
     */
    protected $zipDeliveryResource;

    /**
     * @var CollectionFactory
     */
    protected $zipDeliveryCollectionFactory;

    /**
     * Constructor
     *
     * @param ZipDeliveryFactory $zipDeliveryFactory
     * @param ZipDeliveryResource $zipDeliveryResource
     * @param CollectionFactory $zipDeliveryCollectionFactory
     */
    public function __construct(
        ZipDeliveryFactory $zipDeliveryFactory,
        ZipDeliveryResource $zipDeliveryResource,
        CollectionFactory $zipDeliveryCollectionFactory
    ) {
        $this->zipDeliveryFactory = $zipDeliveryFactory;
        $this->zipDeliveryResource = $zipDeliveryResource;
        $this->zipDeliveryCollectionFactory = $zipDeliveryCollectionFactory;
    }

    /**
     * Get ZIP Delivery by ZIP Code
     *
     * @param string $zipCode
     * @return \Bss\CustomizeDeliveryDate\Model\ZipDelivery|null
     */
    public function getByZipCode($zipCode)
    {
        $zipDelivery = $this->zipDeliveryFactory->create();
        $this->zipDeliveryResource->load($zipDelivery, $zipCode, 'zip_code');
        if (!$zipDelivery->getId()) {
            return null;
        }
        return $zipDelivery;
    }

    /**
     * Save ZIP Delivery
     *
     * @param array $data
     * @return \Bss\CustomizeDeliveryDate\Model\ZipDelivery
     */
    public function save($data)
    {
        $zipDelivery = $this->zipDeliveryFactory->create();
        $zipDelivery->setData($data);
        $this->zipDeliveryResource->save($zipDelivery);
        return $zipDelivery;
    }

    /**
     * Get Delivery Days by ZIP Code
     *
     * @param string $zipCode
     * @return string|null
     */
    public function getDeliveryDaysByZipCode($zipCode)
    {
        $collection = $this->zipDeliveryCollectionFactory->create();

        foreach ($collection as $record) {
            $storedZipCode = $record->getZipCode();

            if ($storedZipCode === $zipCode) {
                return $record->getDeliveryDays();
            }

            if (strpos($storedZipCode, '-') !== false) {
                [$from, $to] = explode('-', $storedZipCode);
                if ($zipCode >= $from && $zipCode <= $to) {
                    return $record->getDeliveryDays();
                }
            }

            if ($storedZipCode === '*') {
                return $record->getDeliveryDays();
            }
        }

        return null;
    }

    /**
     * Get All ZIP Delivery Records
     *
     * @return \Bss\CustomizeDeliveryDate\Model\ResourceModel\ZipDelivery\Collection
     */
    public function getAll()
    {
        $collection = $this->zipDeliveryCollectionFactory->create();
        $collection->setOrder('zip_id', \Magento\Framework\Data\Collection::SORT_ORDER_DESC);
        return $collection->getData();
    }

    /**
     * Delete Zip Delivery items by IDs
     *
     * @param array $zipIds
     * @return int|null Number of deleted records
     */
    public function deleteByIds($zipIds)
    {
        $this->zipDeliveryResource->deleteZipByIds($zipIds);
        return count($zipIds);
    }

    /**
     * Delete all Zip Delivery items
     *
     * @return int|null Number of deleted records
     */
    public function deleteAllItems()
    {
        $collection = $this->zipDeliveryCollectionFactory->create();
        $data = $collection->getData();
        $this->zipDeliveryResource->truncateData();
        return count($data);
    }
}
