<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Checkout Fields for Magento 2
 */

namespace Amasty\Orderattr\Block\Adminhtml\Relation\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ResetButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     * @codeCoverageIgnore
     */
    public function getButtonData()
    {
        return [
            'label' => __('Reset'),
            'class' => 'reset',
            'on_click' => 'location.reload();',
            'sort_order' => 30,
        ];
    }
}
