<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Reports Base for Magento 2
 */

namespace Amasty\Reports\Block\Adminhtml\Rule;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveAndContinueButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [
            'label'      => __('Save and Continue Edit'),
            'class'      => 'save',
            'on_click'   => '',
            'sort_order' => 90,
        ];
    }
}
