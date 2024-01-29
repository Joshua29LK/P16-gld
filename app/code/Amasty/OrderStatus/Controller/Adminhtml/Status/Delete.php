<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Status for Magento 2
 */
namespace Amasty\OrderStatus\Controller\Adminhtml\Status;

use Magento\Backend\App\Action;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Amasty\OrderStatus\Model\ResourceModel\Template
     */
    private $template;

    /**
     * @var \Amasty\OrderStatus\Model\Status
     */
    private $statusFactory;

    /**
     * Delete constructor.
     * @param Action\Context $context
     * @param \Amasty\OrderStatus\Model\ResourceModel\Template $template
     * @param \Amasty\OrderStatus\Model\StatusFactory $statusFactory
     */
    public function __construct(
        Action\Context $context,
        \Amasty\OrderStatus\Model\ResourceModel\Template $template,
        \Amasty\OrderStatus\Model\StatusFactory $statusFactory
    ) {
        $this->template = $template;
        $this->statusFactory = $statusFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $this->template->removeStatusTemplates($id);
                /** @var \Amasty\OrderStatus\Model\Status $status */
                $status = $this->statusFactory->create();
                $status->setId($id);
                //phpcs:ignore
                $status->getResource()->delete($status);
                $this->messageManager->addSuccessMessage(__('The status has been deleted.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $this->resultRedirectFactory->create()
                    ->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }
        $this->messageManager->addErrorMessage(__('Unable to find status to delete.'));
        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_OrderStatus::amostatus');
    }
}
