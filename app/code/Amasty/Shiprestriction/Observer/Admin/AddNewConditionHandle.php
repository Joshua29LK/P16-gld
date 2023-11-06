<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Observer\Admin;

use Amasty\Shiprestriction\Model\Quote\Inventory\MsiModuleStatusInspector;
use Amasty\Shiprestriction\Model\Rule\Condition\Source;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddNewConditionHandle implements ObserverInterface
{
    /**
     * @var MsiModuleStatusInspector
     */
    private $msiModuleStatusInspector;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        MsiModuleStatusInspector $msiModuleStatusInspector,
        RequestInterface $request = null
    ) {
        $this->msiModuleStatusInspector = $msiModuleStatusInspector;
        // OM for backward compatibility
        $this->request = $request ?? ObjectManager::getInstance()->get(RequestInterface::class);
    }

    /**
     * Add new condition by MSI source in Advanced Conditions group
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer): self
    {
        if (!$this->msiModuleStatusInspector->isEnabled()
            || $this->request->getModuleName() !== 'amasty_shiprestriction'
        ) {
            return $this;
        }

        $additional = $observer->getAdditional();
        $conditions = $additional->getConditions();

        if (!is_array($conditions)) {
            return $this;
        }

        foreach ($conditions as &$customConditions) {
            $label = $customConditions['label'] ?? null;
            $values = $customConditions['value'] ?? null;
            $labelToCompare = __('Advanced Conditions');

            if ($label && is_array($values) && $label->getText() === $labelToCompare->getText()) {
                $values[] = [
                    'value' => Source::class,
                    'label' => __('Source')
                ];
                $customConditions['value'] = $values;
                break;
            }
        }

        $additional->setConditions($conditions);

        return $this;
    }
}
