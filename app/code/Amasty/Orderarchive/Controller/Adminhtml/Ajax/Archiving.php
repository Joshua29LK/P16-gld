<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Archive for Magento 2
*/

namespace Amasty\Orderarchive\Controller\Adminhtml\Ajax;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class Archiving extends \Amasty\Orderarchive\Controller\Adminhtml\Archive
{
    /**
     * @var \Amasty\Orderarchive\Helper\Email\Data
     */
    protected $emailHelper;

    /**
     * Archiving constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Amasty\Orderarchive\Helper\Data $helper
     * @param \Amasty\Orderarchive\Api\ArchiveProcessorInterface $orderProcessor
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Amasty\Orderarchive\Controller\Adminhtml\Archive\MassAddToArchive $archive
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Amasty\Orderarchive\Helper\Data $helper,
        \Amasty\Orderarchive\Api\ArchiveProcessorInterface $orderProcessor,
        CollectionFactory $collectionFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Amasty\Orderarchive\Helper\Email\Data $emailHelper
    ) {
        parent::__construct($context, $helper, $orderProcessor, $collectionFactory, $resultPageFactory);
        $this->emailHelper = $emailHelper;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            if ($this->helper->isModuleOn()) {
                $result = $this->orderProcessor->moveAllToArchive();

                if ((int)$result->getOrderIds() === 0) {
                    $this->messageManager->addErrorMessage(__('Orders for archiving is not found!'));
                } else {
                    $this->emailHelper->orderArchivedAfter($result);
                    $this->messageManager->addSuccessMessage(
                        $this->helper->getInformationString($result, self::ADD_TO_ARCHIVE_METHOD_CODE)
                    );
                }
            } else {
                $this->messageManager->addErrorMessage(__('Module is disabled.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $resultRedirect->setRefererUrl();

        return $resultRedirect;
    }
}
