<?php
/**
 * Created by RedChamps.
 * User: Rav
 * Date: 02/11/18
 * Time: 1:18 PM
 */
namespace RedChamps\ShareCart\Controller\Adminhtml\Action\SharedCarts;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use RedChamps\ShareCart\Model\ShareCart;

class MassDelete extends Action
{
    /**
     * @var ShareCart
     */
    protected $shareCart;

    public function __construct(
        ShareCart $shareCart,
        Context $context
    ) {
        $this->shareCart = $shareCart;
        parent::__construct($context);
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('RedChamps_ShareCart::shared_carts');
    }

    public function execute()
    {
        $sharedCartIds = $this->getRequest()->getParam('shared_cart');
        if (!is_array($sharedCartIds)) {
            $this->messageManager->addErrorMessage(_('Please select shared cart(s).'));
        } else {
            if (!empty($sharedCartIds)) {
                try {
                    foreach ($sharedCartIds as $sharedCartId) {
                        $sharedCart = $this->shareCart->load($sharedCartId);
                        $sharedCart->delete();
                    }
                    $this->messageManager->addSuccessMessage(
                        __('Total of %1 record(s) have been deleted.', count($sharedCartIds))
                    );
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            }
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/view');
    }
}
