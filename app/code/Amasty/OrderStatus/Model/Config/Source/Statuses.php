<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Status for Magento 2
 */

namespace Amasty\OrderStatus\Model\Config\Source;

use Amasty\OrderStatus\Block\Adminhtml\Status\Edit\Tab\Email;
use Magento\Framework\Option\ArrayInterface;

class Statuses implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            Email::NOTIFY_DISABLED => __('Disabled'),
            Email::NOTIFY_ENABLED => __('Enabled'),
            Email::NOTIFY_OPTIONAL => __('Optional'),
        ];
    }
}
