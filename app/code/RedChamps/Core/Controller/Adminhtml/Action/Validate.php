<?php
namespace RedChamps\Core\Controller\Adminhtml\Action;

use Magento\Backend\App\AbstractAction;
use Magento\Backend\App\Action;
use RedChamps\Core\Cron\GetUpdates;

class Validate extends AbstractAction
{
    protected $fetchUpdates;

    public function __construct(
        Action\Context $context,
        GetUpdates $getUpdates
    ) {
        $this->fetchUpdates = $getUpdates;
        parent::__construct($context);
    }

    public function execute()
    {
        $isAjax = $this->getRequest()->isAjax();
        $force = $isAjax ? false : true;

        $this->fetchUpdates->execute($force);
        if (!$isAjax) {
            $this->messageManager->getMessages()->clear();
            $this->_redirect('adminhtml/dashboard/index');
        }
    }
}
