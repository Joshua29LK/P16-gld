<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Archive for Magento 2
*/

namespace Amasty\Orderarchive\Controller\Adminhtml\Archive;

class OrderToArchive extends \Amasty\Orderarchive\Controller\Adminhtml\Order
{
    /**
     * @param int $orderId
     */
    public function action($orderId)
    {
        $result = $this->orderProcessor->moveToArchive([$orderId]);
        if (!$result->getOrderIds()) {
            $this->messageManager->addErrorMessage(__('Something went wrong: orders haven\'t been moved.'
                . ' Check settings of extension and be sure that selected orders match the conditions.'));
        } else {
            $this->messageManager->addSuccessMessage(
                $this->helper->getInformationString($result, self::ADD_TO_ARCHIVE_METHOD_CODE)
            );
        }
    }
}
