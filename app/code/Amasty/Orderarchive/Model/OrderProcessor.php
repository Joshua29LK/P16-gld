<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Archive for Magento 2
*/

namespace Amasty\Orderarchive\Model;

class OrderProcessor implements \Amasty\Orderarchive\Api\ArchiveProcessorInterface
{
    public const ADD_TO_ARCHIVE_METHOD_CODE = 'addToArchive';

    public const REMOVE_FROM_ARCHIVE_METHOD_CODE = 'removeFromArchive';

    public const REMOVE_PERMANENTLY_METHOD_CODE = 'removePermanently';

    /**
     * @var \Amasty\Orderarchive\Model\ArchiveFactory
     */
    private $archiveFactory;

    /**
     * @var \Amasty\Orderarchive\Helper\Data
     */
    private $helper;

    /**
     * @var \Amasty\Orderarchive\Api\Data\ArchiveAffectedIdsInterfaceFactory
     */
    private $affectedIdsFactory;

    public function __construct(
        \Amasty\Orderarchive\Model\ArchiveFactory $archiveFactory,
        \Amasty\Orderarchive\Api\Data\ArchiveAffectedIdsInterfaceFactory $affectedIdsFactory,
        \Amasty\Orderarchive\Helper\Data $helper
    ) {
        $this->archiveFactory = $archiveFactory;
        $this->helper         = $helper;
        $this->affectedIdsFactory = $affectedIdsFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function moveToArchive($orderIds)
    {
        return $this->process($orderIds, self::ADD_TO_ARCHIVE_METHOD_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function moveAllToArchive()
    {
        return $this->process([], self::ADD_TO_ARCHIVE_METHOD_CODE, true);
    }

    /**
     * {@inheritdoc}
     */
    public function removePermanently($orderIds)
    {
        return $this->process($orderIds, self::REMOVE_PERMANENTLY_METHOD_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function removeFromArchive($orderIds)
    {
        return $this->process($orderIds, self::REMOVE_FROM_ARCHIVE_METHOD_CODE);
    }

    /**
     * @param array  $selectedOrders
     * @param string $method
     * @param bool   $moveAll
     *
     * @return \Amasty\Orderarchive\Api\Data\ArchiveAffectedIdsInterface
     */
    public function process(array $selectedOrders, $method = self::ADD_TO_ARCHIVE_METHOD_CODE, $moveAll = false)
    {
        $result = $this->affectedIdsFactory->create();

        if (!$moveAll && count($selectedOrders) < 1) {
            return $result;
        }

        if ($moveAll) {
            $orderModel = $this->archiveFactory->getArchiveModels('order');
            /** @method \Amasty\Orderarchive\Model\ResourceModel\OrderGrid getProcessor() */
            $selectedOrders = $orderModel->getProcessor()->getAffectedOrderIds($method, $orderModel->getSourceTable());
        }

        $archiveSalesTablesList = $this->helper->getArchiveListModel();

        foreach ($archiveSalesTablesList as $table) {
            if (empty($selectedOrders)) {
                break;
            }

            $type = $this->archiveFactory->getTypeFromConstant($table);
            $archiveModel = $this->archiveFactory->getArchiveModels($type);

            switch ($method) {
                case self::ADD_TO_ARCHIVE_METHOD_CODE:
                    $result[$type] = $archiveModel->getProcessor()
                        ->addToArchive($archiveModel->toArray(), $selectedOrders);
                    break;
                case self::REMOVE_FROM_ARCHIVE_METHOD_CODE:
                    $result[$type] = $archiveModel->getProcessor()
                        ->removeFromArchive($archiveModel->toArray(), $selectedOrders);
                    break;
                case self::REMOVE_PERMANENTLY_METHOD_CODE:
                    if ('order' == $type) {
                        $orderInfo = $archiveModel->getProcessor()->removePermanently($selectedOrders);
                        $result->setData($orderInfo);
                    }
                    break;
            }

            if ('order' == $type) {
                $selectedOrders = $result[$type];
            }
        }

        return $result;
    }
}
