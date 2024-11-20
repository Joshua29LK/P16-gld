<?php
namespace Bss\CustomizeDeliveryDate\Controller\Adminhtml\Zip;

use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use Bss\CustomizeDeliveryDate\Model\ZipDeliveryRepository;
use Magento\Framework\Controller\ResultFactory;

class MassDelete extends Action
{
    /**
     * @var ZipDeliveryRepository
     */
    protected $zipDeliveryRepository;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * Constructor
     *
     * @param Action\Context $context
     * @param ZipDeliveryRepository $zipDeliveryRepository
     * @param Filter $filter
     */
    public function __construct(
        Action\Context $context,
        ZipDeliveryRepository $zipDeliveryRepository,
        Filter $filter
    ) {
        parent::__construct($context);
        $this->zipDeliveryRepository = $zipDeliveryRepository;
        $this->filter = $filter;
    }

    /**
     * Execute MassDelete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $zipIds = $this->getRequest()->getParam('selected');

        try {
            if (empty($zipIds)) {
                $deletedCount = $this->zipDeliveryRepository->deleteAllItems();
            } else {
                $deletedCount = $this->zipDeliveryRepository->deleteByIds($zipIds);
            }
            $this->messageManager->addSuccessMessage(
                __('A total of %1 item(s) have been deleted.', $deletedCount)
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error occurred while deleting items: %1', $e->getMessage()));
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
    }

    /**
     * Check ACL permissions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Bss_CustomizeDeliveryDate::delete');
    }
}