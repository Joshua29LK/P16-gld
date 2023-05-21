<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

namespace Amasty\Flags\Controller\Adminhtml\Flag;

use Amasty\Flags\Api\FlagRepositoryInterface;
use Amasty\Flags\Controller\Adminhtml\Flag as FlagAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

class Delete extends FlagAction
{
    /**
     * @var FlagRepositoryInterface
     */
    private $flagRepository;

    public function __construct(
        Context $context,
        FlagRepositoryInterface $flagRepository
    ) {
        parent::__construct($context);
        $this->flagRepository = $flagRepository;
    }

    public function execute()
    {
        try {
            $id = (int)$this->getRequest()->getParam('id');
            $flag = $this->flagRepository->get($id);

            if (!$flag->getId()) {
                throw new LocalizedException(__('We can\'t find a flag to delete.'));
            }

            $this->flagRepository->delete($flag);

            $this->messageManager->addSuccessMessage(__('The flag has been deleted.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            if (isset($id) && $id) {
                return $this->resultRedirectFactory->create()->setPath('*/*/edit', ['id' => $id]);
            }
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
