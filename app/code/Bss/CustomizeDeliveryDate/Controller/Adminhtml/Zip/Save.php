<?php
namespace Bss\CustomizeDeliveryDate\Controller\Adminhtml\Zip;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Bss\CustomizeDeliveryDate\Model\ZipDeliveryRepository
     */
    protected $zipRepository;

    /**
     * Constructor
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Bss\CustomizeDeliveryDate\Model\ZipRepository $zipRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Bss\CustomizeDeliveryDate\Model\ZipDeliveryRepository $zipRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->zipRepository = $zipRepository;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            try {
                $zipCode = '';

                if (isset($data['zip_is_range'])) {
                    $rangeFrom = isset($data['range_from']) ? trim($data['range_from']) : null;
                    $rangeTo = isset($data['range_to']) ? trim($data['range_to']) : null;

                    if (empty($rangeFrom) || empty($rangeTo)) {
                        throw new LocalizedException(__('Both Range From and Range To are required.'));
                    }
                    $zipCode = $rangeFrom . '-' . $rangeTo;
                } else {
                    $zipCode = isset($data['zip_code']) ? trim($data['zip_code']) : null;

                    if (empty($zipCode)) {
                        throw new LocalizedException(__('ZIP Code is required.'));
                    }
                }
                
                $data['zip_code'] = $zipCode;
                
                if (isset($data['delivery_days']) && is_array($data['delivery_days'])) {
                    $data['delivery_days'] = implode(',', $data['delivery_days']);
                }
                
                $this->zipRepository->save($data);

                $this->messageManager->addSuccessMessage(__('The ZIP delivery data has been saved.'));
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('An error occurred while saving the ZIP delivery data.'));
            }

            return $resultRedirect->setPath('*/*/edit', ['zip_id' => $this->getRequest()->getParam('zip_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}