<?php

namespace Balticode\CategoryConfigurator\Controller\Adminhtml\Step;

use Balticode\CategoryConfigurator\Api\Data\StepInterface;
use Balticode\CategoryConfigurator\Api\StepRepositoryInterface;
use Balticode\CategoryConfigurator\Controller\Adminhtml\Step;
use Balticode\CategoryConfigurator\Model\StepFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Step
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

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
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param StepRepositoryInterface $stepRepository
     * @param StepFactory $stepFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        StepRepositoryInterface $stepRepository,
        StepFactory $stepFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->stepRepository = $stepRepository;
        $this->stepFactory = $stepFactory;

        parent::__construct($context, $coreRegistry);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('step_id');
        $step = $this->getStep($id);

        if ($id && !$step->getStepId()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage(__('This step no longer exists.'));

            return $resultRedirect->setPath('*/*/');
        }

        $this->_coreRegistry->register('balticode_categoryconfigurator_step', $step);

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Step') : __('New Step'),
            $id ? __('Edit Step') : __('New Step')
        );

        $resultPage->getConfig()->getTitle()->prepend(__('Steps'));

        $resultPage->getConfig()->getTitle()->prepend(
            $step->getConfiguratorId() ? $step->getTitle() : __('New Step')
        );

        return $resultPage;
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
