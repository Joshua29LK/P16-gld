<?php

namespace Balticode\CategoryConfigurator\Controller\Adminhtml\Configurator;

use Balticode\CategoryConfigurator\Api\ConfiguratorRepositoryInterface;
use Balticode\CategoryConfigurator\Api\Data\ConfiguratorInterface;
use Balticode\CategoryConfigurator\Controller\Adminhtml\Configurator;
use Balticode\CategoryConfigurator\Model\ConfiguratorFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Configurator
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ConfiguratorRepositoryInterface
     */
    protected $configuratorRepository;

    /**
     * @var ConfiguratorFactory
     */
    protected $configuratorFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param ConfiguratorRepositoryInterface $configuratorRepository
     * @param ConfiguratorFactory $configuratorFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        ConfiguratorRepositoryInterface $configuratorRepository,
        ConfiguratorFactory $configuratorFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->configuratorRepository = $configuratorRepository;
        $this->configuratorFactory = $configuratorFactory;

        parent::__construct($context, $coreRegistry);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('configurator_id');
        $configurator = $this->getConfigurator($id);

        if ($id && !$configurator->getConfiguratorId()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage(__('This configurator no longer exists.'));

            return $resultRedirect->setPath('*/*/');
        }

        $this->_coreRegistry->register('balticode_categoryconfigurator_configurator', $configurator);
        
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Configurator') : __('New Configurator'),
            $id ? __('Edit Configurator') : __('New Configurator')
        );

        $resultPage->getConfig()->getTitle()->prepend(__('Configurators'));

        $resultPage->getConfig()->getTitle()->prepend(
            $configurator->getConfiguratorId() ? $configurator->getTitle() : __('New Configurator')
        );

        return $resultPage;
    }

    /**
     * @param int|null $id
     * @return ConfiguratorInterface
     */
    protected function getConfigurator($id)
    {
        try {
            return $this->configuratorRepository->getById($id);
        } catch (LocalizedException $e) {
            return $this->configuratorFactory->create();
        }
    }
}
