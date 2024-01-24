<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Status for Magento 2
 */

namespace Amasty\OrderStatus\Plugin\Sales\Model\Order\Status;

use Amasty\OrderStatus\Model\AmOrderStatusRegistry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order\Status;

class GetStoreLabels
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var AmOrderStatusRegistry
     */
    private $amOrderStatusRegistry;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Registry $coreRegistry,
        AmOrderStatusRegistry $amOrderStatusRegistry
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->coreRegistry = $coreRegistry;
        $this->amOrderStatusRegistry = $amOrderStatusRegistry;
    }

    public function afterGetStoreLabels(Status $subject, array $result): array
    {
        if (!$result && $amastyStatus = $this->amOrderStatusRegistry->get($subject->getStatus())) {
            $storeId = $this->coreRegistry->registry('amorderstatus_store_id');
            $orderState = $this->coreRegistry->registry('amorderstatus_state');
            $hideState = $this->scopeConfig->getValue('amostatus/general/hide_state');

            if ($orderState) {
                $result[$storeId] = ($hideState ? '' : ucfirst($orderState) . ': ') . $amastyStatus->getStatus();
            }
        }

        return $result;
    }
}
