<?php
namespace  Magedelight\Megamenu\Controller\Adminhtml\Cache;

use Magento\Backend\App\Action;

/**
 * Class Cleanmenu
 * @package Magedelight\Megamenu\Controller\Adminhtml\Cache
 */
class Cleanmenu extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magedelight_Megamenu::cleanmenu';

    /**
     * @var \Magedelight\Megamenu\Helper\Cache
     */
    protected $cacheHelper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magedelight\Megamenu\Helper\Cache $cacheHelper
    ) {
        parent::__construct($context);
        $this->resultJsonFactory= $resultJsonFactory;
        $this->cacheHelper = $cacheHelper;
    }

    /**
     * Index action
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $resultJson = $this->resultJsonFactory->create();
        $sendData = [];
        try {
            if ($params['type'] == 'storeMenu') {
                $this->cacheHelper->updateVariableByCode($this->cacheHelper->getStoreMenuKey());
            }
            $sendData = [
                'messages' => 'Successfully.',
                'error' => false
            ];
        } catch (\Exception $e) {
            $sendData = [
                'messages' => 'Please try again.',
                'error' => true
            ];
        }
        return $resultJson->setData($sendData);
    }
}
