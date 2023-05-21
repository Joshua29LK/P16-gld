<?php

namespace Balticode\CategoryConfigurator\Controller\Adminhtml\Step;

use Balticode\CategoryConfigurator\Api\StepRepositoryInterface;
use Balticode\CategoryConfigurator\Controller\Adminhtml\Step;
use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;

class Delete extends Step
{
    /**
     * @var StepRepositoryInterface
     */
    protected $stepRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param StepRepositoryInterface $stepRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        StepRepositoryInterface $stepRepository,
        LoggerInterface $logger
    ) {
        $this->stepRepository = $stepRepository;
        $this->logger = $logger;

        parent::__construct($context, $coreRegistry);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $id = $this->getRequest()->getParam('step_id');

        if (!$id) {
            $this->messageManager->addErrorMessage(__('We can\'t find a step to delete.'));

            return $resultRedirect->setPath('*/*/');
        }

        try {
            $step = $this->stepRepository->getById($id);
            $this->stepRepository->delete($step);

            $this->messageManager->addSuccessMessage(__('You deleted the step.'));

            $configuratorId = $step->getConfiguratorId();

            return $resultRedirect->setPath('*/configurator/edit',  ['configurator_id' => $configuratorId]);
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
            $this->messageManager->addErrorMessage(__('We were unable to delete this configurator.'));

            return $resultRedirect->setPath('*/*/edit', ['step_id' => $id]);
        }
    }
}
