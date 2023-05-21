<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

namespace Amasty\Flags\Controller\Adminhtml\Column;

use Amasty\Flags\Api\ColumnRepositoryInterface;
use Amasty\Flags\Model\Column;
use Magento\Backend\App\Action\Context;
use Amasty\Flags\Controller\Adminhtml\Column as ColumnAction;

class Save extends ColumnAction
{
    /**
     * @var ColumnRepositoryInterface
     */
    private $columnRepository;

    public function __construct(
        Context $context,
        ColumnRepositoryInterface $columnRepository
    ) {
        parent::__construct($context);
        $this->columnRepository = $columnRepository;
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = (int)$this->getRequest()->getParam('id');
            /** @var Column $model */
            $model = $this->columnRepository->get($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This column no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            try {
                $this->columnRepository->save($model);

                $model->reassignFlags(isset($data['apply_flag']) ? $data['apply_flag'] : []);

                $this->messageManager->addSuccessMessage(__('The column has been saved.'));

                $this->_session->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_session->setFormData($data);

                return $resultRedirect->setPath('*/*/edit', [
                    'id' => $this->getRequest()->getParam('id')
                ]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
