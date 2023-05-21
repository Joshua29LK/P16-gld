<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

namespace Amasty\Flags\Controller\Adminhtml\Column;

use Amasty\Flags\Api\ColumnRepositoryInterface;
use Amasty\Flags\Controller\Adminhtml\Column as ColumnAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

class Delete extends ColumnAction
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
        try {
            $id = (int)$this->getRequest()->getParam('id');
            $column = $this->columnRepository->get($id);

            if (!$column->getId()) {
                throw new LocalizedException(__('We can\'t find a column to delete.'));
            }

            $this->columnRepository->delete($column);

            $this->messageManager->addSuccessMessage(__('The column has been deleted.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            if (isset($id) && $id) {
                return $this->resultRedirectFactory->create()->setPath('*/*/edit', ['id' => $id]);
            }
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
