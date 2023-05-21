<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Archive for Magento 2
*/

namespace Amasty\Orderarchive\Controller\Adminhtml\Order;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Pdfdocs extends \Magento\Sales\Controller\Adminhtml\Order\Pdfdocs
{
    /**
     *
     * @return null|string
     */
    protected function getComponentRefererUrl()
    {
        return $this->filter->getComponentRefererUrl()?: 'amastyorderarchive/*/';
    }
}
