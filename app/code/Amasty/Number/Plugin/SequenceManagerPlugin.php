<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Custom Order Number for Magento 2
*/

namespace Amasty\Number\Plugin;

use Amasty\Number\Model\ConfigProvider;
use Amasty\Number\Model\SequenceStorage;
use Magento\SalesSequence\Model\Manager;

class SequenceManagerPlugin
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var SequenceStorage
     */
    private $sequenceStorage;

    public function __construct(
        ConfigProvider $configProvider,
        SequenceStorage $sequenceStorage
    ) {
        $this->configProvider = $configProvider;
        $this->sequenceStorage = $sequenceStorage;
    }

    /**
     * @param Manager $subject
     * @param $entityType
     * @param $storeId
     */
    public function beforeGetSequence(Manager $subject, $entityType, $storeId)
    {
        try {
            $this->configProvider->setStoreId($storeId);
            $this->sequenceStorage->setEntityType((string)$entityType);
        } catch (\Exception $e) {
            null;
        }
    }
}
