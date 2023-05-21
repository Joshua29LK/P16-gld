<?php

namespace Balticode\CategoryConfigurator\Controller\Adminhtml\Step;

use Balticode\CategoryConfigurator\Api\Data\StepInterface;
use Balticode\CategoryConfigurator\Api\StepRepositoryInterface;
use Balticode\CategoryConfigurator\Model\StepFactory;
use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var StepRepositoryInterface
     */
    protected $stepRepository;

    /**
     * @var StepFactory
     */
    protected $stepFactory;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param StepRepositoryInterface $stepRepository
     * @param StepFactory $stepFactory
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        StepRepositoryInterface $stepRepository,
        StepFactory $stepFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->stepRepository = $stepRepository;
        $this->stepFactory = $stepFactory;

        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        $id = $this->getRequest()->getParam('step_id');
        $step = $this->getStep($id);

        if ($id && !$step->getStepId()) {
            $this->messageManager->addErrorMessage(__('This step no longer exists.'));

            return $resultRedirect->setPath('*/*/');
        }

        $step->setData($data);

        try {
            $this->stepRepository->save($step);
            $this->messageManager->addSuccessMessage(__('You saved the step.'));

            $this->dataPersistor->clear('balticode_categoryconfigurator_step');

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['step_id' => $step->getStepId()]);
            }

            $configuratorId = $step->getConfiguratorId();

            return $resultRedirect->setPath('*/configurator/edit',  ['configurator_id' => $configuratorId]);
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the step.'));
        }

        $this->dataPersistor->set('balticode_categoryconfigurator_step', $data);

        return $resultRedirect->setPath('*/*/edit', ['step_id' => $this->getRequest()->getParam('step_id')]);
    }

    /**
     * @param int|null $id
     * @return StepInterface
     */
    protected function getStep($id)
    {
        try {
            return $this->stepRepository->getById($id);
        } catch (LocalizedException $e) {
            return $this->stepFactory->create();
        }
    }
}
