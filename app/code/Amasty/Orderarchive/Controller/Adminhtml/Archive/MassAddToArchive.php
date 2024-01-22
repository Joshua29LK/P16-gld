<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Archive for Magento 2
 */

namespace Amasty\Orderarchive\Controller\Adminhtml\Archive;

class MassAddToArchive extends \Amasty\Orderarchive\Controller\Adminhtml\Action
{
    /**
     * @param array $selectedIds
     * @return void
     */
    protected function massAction($selectedIds)
    {
        $result = $this->orderProcessor->moveToArchive($selectedIds);
        $this->messageManager->addSuccessMessage(
            $this->helper->getInformationString($result, self::ADD_TO_ARCHIVE_METHOD_CODE)
        );
    }
}
