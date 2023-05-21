<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Order Status for Magento 2
 */

namespace Amasty\OrderStatus\Plugin\Sales\Model\Order\Status;

use Amasty\OrderStatus\Model\ResourceModel\Status\CollectionFactory;
use Amasty\OrderStatus\Model\Status as AmastyOrderStatus;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Model\Order\Status as MagentoOrderStatus;

class LoadAmastyStatus
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var CollectionFactory
     */
    private $amastyStatusCollectionFactory;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $collectionFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->amastyStatusCollectionFactory = $collectionFactory;
    }

    public function afterLoad(
        MagentoOrderStatus $subject,
        MagentoOrderStatus $result,
        string $code
    ): AbstractModel {
        $amastyStatus = false;

        if (!$result->getLabel() && $code) {
            $amastyStatus = $this->getAmastyStatus($code);
        }

        return $amastyStatus ?: $result;
    }

    private function getAmastyStatus(string $code): ?AmastyOrderStatus
    {
        $statusesCollection = $this->amastyStatusCollectionFactory->create();
        $statusesCollection->addFieldToFilter('is_active', ['eq' => 1]);
        $statusesCollection->addFieldToFilter('is_system', ['eq' => 0]);

        $hideState = $this->scopeConfig->getValue('amostatus/general/hide_state');
        $amastyStatus = null;

        if ($statusesCollection->getSize() > 0) {
            foreach ($statusesCollection->getStates() as $state) {
                foreach ($statusesCollection as $status) {
                    $parentStates = [];

                    if ($status->getParentState()) {
                        $parentStates = explode(',', $status->getParentState());
                    }

                    if (!$parentStates || in_array($state['value'], $parentStates)) {
                        $elementName = $state['value'] . '_' . $status->getAlias();

                        if ($code === $elementName) {
                            $statusLabel = ($hideState ? '' : $state['label'] . ': ') . __($status->getStatus());
                            $amastyStatus = $status;
                            $amastyStatus->setLabel($statusLabel);
                            $amastyStatus->setStoreLabel($statusLabel);

                            break(2);
                        }
                    }
                }
            }
        }

        return $amastyStatus;
    }
}
