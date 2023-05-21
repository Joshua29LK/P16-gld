<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Custom Checkout Fields for Magento 2
 */

namespace Amasty\Orderattr\Model\Value\Metadata\Form\CollectionFilter;

use Amasty\Orderattr\Model\ResourceModel\Attribute\Collection;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;

class CheckoutStep implements FilterInterface
{
    /**
     * @var State
     */
    private $appState;

    public function __construct(State $appState)
    {
        $this->appState = $appState;
    }

    public function apply(Collection $collection): void
    {
        if ($this->appState->getAreaCode() !== Area::AREA_ADMINHTML) {
            $collection->addFilterUnassignedOnCheckout();
        }
    }
}
