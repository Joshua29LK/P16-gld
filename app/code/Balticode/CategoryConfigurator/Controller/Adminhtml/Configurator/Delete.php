<?php

namespace Balticode\CategoryConfigurator\Controller\Adminhtml\Configurator;

use Balticode\CategoryConfigurator\Api\ConfiguratorRepositoryInterface;
use Balticode\CategoryConfigurator\Controller\Adminhtml\Configurator;
use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;

class Delete extends Configurator
{
    /**
     * @var ConfiguratorRepositoryInterface
     */
    protected $configuratorRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param ConfiguratorRepositoryInterface $configuratorRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ConfiguratorRepositoryInterface $configuratorRepository,
        LoggerInterface $logger
    ) {
        $this->configuratorRepository = $configuratorRepository;
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

        $id = $this->getRequest()->getParam('configurator_id');

        if (!$id) {
            $this->messageManager->addErrorMessage(__('We can\'t find a configurator to delete.'));

            return $resultRedirect->setPath('*/*/');
        }

        try {
            $configurator = $this->configuratorRepository->getById($id);
            $this->configuratorRepository->delete($configurator);

            $this->messageManager->addSuccessMessage(__('You deleted the configurator.'));

            return $resultRedirect->setPath('*/*/');
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
            $this->messageManager->addErrorMessage(__('We were unable to delete this configurator.'));

            return $resultRedirect->setPath('*/*/edit', ['configurator_id' => $id]);
        }
    }
}
