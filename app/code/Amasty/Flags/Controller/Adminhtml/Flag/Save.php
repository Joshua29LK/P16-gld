<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

namespace Amasty\Flags\Controller\Adminhtml\Flag;

use Amasty\Flags\Api\ColumnRepositoryInterface;
use Amasty\Flags\Api\FlagRepositoryInterface;
use Amasty\Flags\Controller\Adminhtml\Flag as FlagAction;
use Amasty\Flags\Model\Column;
use Amasty\Flags\Model\Flag;
use Amasty\Flags\Model\ResourceModel\Flag as FlagResource;
use Magento\Backend\App\Action\Context;

class Save extends FlagAction
{
    /**
     * @var FlagRepositoryInterface
     */
    private $flagRepository;
    /**
     * @var FlagResource
     */
    private $flagResource;
    /**
     * @var ColumnRepositoryInterface
     */
    private $columnRepository;

    public function __construct(
        Context $context,
        FlagRepositoryInterface $flagRepository,
        FlagResource $flagResource,
        ColumnRepositoryInterface $columnRepository
    ) {
        parent::__construct($context);
        $this->flagRepository = $flagRepository;
        $this->flagResource = $flagResource;
        $this->columnRepository = $columnRepository;
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = (int)$this->getRequest()->getParam('id');
            /** @var Flag $model */
            $model = $this->flagRepository->get($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This flag no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            foreach (['apply_status', 'apply_shipping', 'apply_payment'] as $field) {
                if (isset($data[$field])) {
                    $data[$field] = implode(',', $data[$field]);
                } else {
                    $data[$field] = '';
                }
            }

            $model->setData($data);
            if (!$data['apply_column']) {
                $model->unsetData('apply_column');
            }

            try {
                $this->flagRepository->save($model);

                if ($data['apply_column']) {
                    /** @var Column $column */
                    $column = $this->columnRepository->get($data['apply_column']);
                    $column->assignFlags([$model->getId()]);
                }

                $this->messageManager->addSuccessMessage(__('The flag has been saved.'));

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
