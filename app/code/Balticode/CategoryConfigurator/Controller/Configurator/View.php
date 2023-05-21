<?php

namespace Balticode\CategoryConfigurator\Controller\Configurator;

use Balticode\CategoryConfigurator\Api\ConfiguratorRepositoryInterface;
use Balticode\CategoryConfigurator\Api\Data\ConfiguratorInterface;
use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;

class View extends Action
{
    const CONFIGURATOR_NOT_FOUND_MESSAGE = 'We were unable to find the requested configurator.';
    
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ConfiguratorRepositoryInterface
     */
    protected $configuratorRepository;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ConfiguratorRepositoryInterface $configuratorRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ConfiguratorRepositoryInterface $configuratorRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->configuratorRepository = $configuratorRepository;

        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        try {
            $configurator = $this->processConfiguratorExistenceCheck();
        } catch (Exception $exception) {
            return $this->processUnsuccessfulRequest($exception->getMessage());
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set($configurator->getTitle());

        return $resultPage;
    }

    /**
     * @return ConfiguratorInterface
     * @throws Exception
     */
    protected function processConfiguratorExistenceCheck()
    {
        $configuratorId = $this->getRequest()->getParam(ConfiguratorInterface::CONFIGURATOR_ID);

        if (!$configuratorId) {
            throw new Exception(Cart::ERROR_MESSAGE_INCORRECT_PARAMETERS);
        }

        try {
            $configurator = $this->configuratorRepository->getById($configuratorId);
        } catch (LocalizedException $e) {
            throw new Exception(self::CONFIGURATOR_NOT_FOUND_MESSAGE);
        }

        if (!$configurator->getConfiguratorId() || !$configurator->getEnable()) {
            throw new Exception(self::CONFIGURATOR_NOT_FOUND_MESSAGE);
        }

        return $configurator;
    }

    /**
     * @param string $errorMessage
     * @return ResultInterface
     */
    protected function processUnsuccessfulRequest($errorMessage)
    {
        $this->messageManager->addErrorMessage(__($errorMessage));

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        return $resultRedirect;
    }
}
