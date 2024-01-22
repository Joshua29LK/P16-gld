<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Archive for Magento 2
 */

namespace Amasty\Orderarchive\Controller\Adminhtml\Archive;

class OrderFromArchive extends \Amasty\Orderarchive\Controller\Adminhtml\Order
{
    /**
     * @param int $orderId
     */
    public function action($orderId)
    {
        $result = $this->orderProcessor->removeFromArchive([$orderId]);
        if (!$result->getOrderIds()) {
            $this->messageManager->addErrorMessage(__('Something went wrong: orders haven\'t been moved.'
                . ' Check settings of extension and be sure that selected orders match the conditions.'));
        } else {
            $this->messageManager->addSuccessMessage(
                $this->helper->getInformationString($result, self::REMOVE_FROM_ARCHIVE_METHOD_CODE)
            );
        }
    }
}
