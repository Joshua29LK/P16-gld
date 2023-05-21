<?php

namespace Balticode\CategoryConfigurator\Controller\Adminhtml\Configurator;

use Balticode\CategoryConfigurator\Api\ConfiguratorRepositoryInterface;
use Balticode\CategoryConfigurator\Api\Data\ConfiguratorInterface;
use Balticode\CategoryConfigurator\Model\Configurator\DefaultStepsProcessor;
use Balticode\CategoryConfigurator\Model\Configurator\ImageProcessor;
use Balticode\CategoryConfigurator\Model\ConfiguratorFactory;
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
     * @var DefaultStepsProcessor
     */
    protected $defaultStepsProcessor;

    /**
     * @var ImageProcessor
     */
    protected $imageProcessor;

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
     * @param DataPersistorInterface $dataPersistor
     * @param DefaultStepsProcessor $defaultStepsProcessor
     * @param ImageProcessor $imageProcessor
     * @param ConfiguratorRepositoryInterface $configuratorRepository
     * @param ConfiguratorFactory $configuratorFactory
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        DefaultStepsProcessor $defaultStepsProcessor,
        ImageProcessor $imageProcessor,
        ConfiguratorRepositoryInterface $configuratorRepository,
        ConfiguratorFactory $configuratorFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->defaultStepsProcessor = $defaultStepsProcessor;
        $this->imageProcessor = $imageProcessor;
        $this->configuratorRepository = $configuratorRepository;
        $this->configuratorFactory = $configuratorFactory;

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

        $id = $this->getRequest()->getParam('configurator_id');
        $configurator = $this->getConfigurator($id);

        if ($id && !$configurator->getConfiguratorId()) {
            $this->messageManager->addErrorMessage(__('This configurator no longer exists.'));

            return $resultRedirect->setPath('*/*/');
        }

        if (isset($data['image_name'][0])) {
            $data['image_name'] = $this->getImageName($data['image_name'][0]);
        }

        $configurator->setData($data);

        try {
            $this->configuratorRepository->save($configurator);
            $this->messageManager->addSuccessMessage(__('You saved the configurator.'));

            $this->defaultStepsProcessor->processDefaultStepsRequest($configurator);
            $this->imageProcessor->removeTemporaryImages();

            $this->dataPersistor->clear('balticode_categoryconfigurator_configurator');

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['configurator_id' => $configurator->getConfiguratorId()]);
            }

            return $resultRedirect->setPath('*/*/');
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Configurator.'));
        }

        $this->dataPersistor->set('balticode_categoryconfigurator_configurator', $data);

        return $resultRedirect->setPath('*/*/edit', ['configurator_id' => $id]);
    }

    /**
     * @param array $data
     * @return string
     */
    protected function getImageName($data)
    {
        if (!isset($data['name'])) {
            return '';
        }

        return $data['name'];
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
