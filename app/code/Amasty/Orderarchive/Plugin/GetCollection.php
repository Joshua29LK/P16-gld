<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Archive for Magento 2
 */

namespace Amasty\Orderarchive\Plugin;

use Amasty\Orderarchive\Helper\Data;

class GetCollection
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var \Amasty\Orderarchive\Model\ResourceModel\OrderGrid
     */
    protected $orderGrid;

    /**
     * GetCollection constructor.
     * @param Data $helper
     */
    public function __construct(
        Data $helper,
        \Amasty\Orderarchive\Model\ResourceModel\OrderGrid $orderGrid
    ) {
        $this->helper = $helper;
        $this->orderGrid = $orderGrid;
    }

    /**
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $subject
     * @param \Magento\Sales\Model\ResourceModel\Order\Collection $collection
     * @return mixed
     */
    public function afterCreate(\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $subject, $collection)
    {
        if ($collection->getSize() &&
            $this->helper->getConfigValueByPath(
                \Amasty\Orderarchive\Helper\Data::CONFIG_PATH_GENERAL_ENABLE_MASSFILTER
            )
        ) {
            $archiveIds = $this->orderGrid->getAllOrdersIds();

            if ($archiveIds) {
                $collection->addFieldToFilter(
                    \Amasty\Orderarchive\Model\ArchiveAbstract::ARCHIVE_ENTITY_ID,
                    ['nin' => $archiveIds]
                );
            }
        }

        return $collection;
    }
}
