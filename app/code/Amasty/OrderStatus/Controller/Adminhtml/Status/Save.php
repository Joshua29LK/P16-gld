<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Status for Magento 2
 */
namespace Amasty\OrderStatus\Controller\Adminhtml\Status;

use Magento\Backend\App\Action\Context;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Amasty\OrderStatus\Model\StatusFactory
     */
    private $statusFactory;

    /**
     * @var \Amasty\OrderStatus\Model\TemplateFactory
     */
    private $templateFactory;

    public function __construct(
        Context $context,
        \Amasty\OrderStatus\Model\StatusFactory $statusFactory,
        \Amasty\OrderStatus\Model\TemplateFactory $templateFactory
    ) {
        parent::__construct($context);
        $this->statusFactory = $statusFactory;
        $this->templateFactory = $templateFactory;
    }

    public function execute()
    {
        /** @var \Amasty\OrderStatus\Model\Status $status */
        $status = $this->statusFactory->create();

        $alias = '';
        if ($id = $this->getRequest()->getParam('id')) {
            $status->load($id);
            $alias = $status->getAlias();
        }
        try {
            $data = $this->getRequest()->getPostValue();
            if (array_key_exists('parent_state', $data) && is_array($data['parent_state'])) {
                $data['parent_state'] = implode(',', $data['parent_state']);
            }

            $status->setData($data);
            if ($id) {
                $status->setId($id);
                $status->setAlias($alias);
            }
            $storeEmailTemplate = [];
            if (isset($data['store_template'])) {
                $storeEmailTemplate = $data['store_template'];
                unset($data['store_template']);
            }
            $status->save();

            /** @var \Amasty\OrderStatus\Model\Template $template */
            $template = $this->templateFactory->create();
            $template->saveTemplates($storeEmailTemplate, $status);
            $this->messageManager->addSuccess(__('The status has been saved.'));
            if ($this->getRequest()->getParam('back')) {
                return $this->resultRedirectFactory->create()->setPath('*/*/edit', ['id' => $status->getId()]);
            } else {
                return $this->resultRedirectFactory->create()->setPath('*/*');
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $this->resultRedirectFactory->create()
                ->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_OrderStatus::amostatus');
    }
}
