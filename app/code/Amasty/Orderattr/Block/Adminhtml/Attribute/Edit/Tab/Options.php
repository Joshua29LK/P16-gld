<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Checkout Fields for Magento 2
 */

namespace Amasty\Orderattr\Block\Adminhtml\Attribute\Edit\Tab;

use Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\AbstractOptions;

class Options extends AbstractOptions
{
    protected function _prepareLayout()
    {
        $this->addChild('labels', 'Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\Labels');
        $this->addChild('tooltip', 'Amasty\Orderattr\Block\Adminhtml\Attribute\Edit\Tab\Options\Tooltip');
        $this->addChild('options', 'Amasty\Orderattr\Block\Adminhtml\Attribute\Edit\Tab\Options\Options');

        return $this;
    }
}
