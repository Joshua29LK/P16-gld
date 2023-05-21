<?php
namespace RedChamps\Core\Observer;

use Magento\Backend\Model\Auth\Session as BackendAuthSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use RedChamps\Core\Model\FeedFactory;

/*
 * Package: Core
 * Class: Event1
 * Company: RedChamps
 * author: rav(rav@redchamps.com)
 * */
class Event1 implements ObserverInterface
{
    /**
     * @var BackendAuthSession
     */
    protected $_backendAuthSession;

    /**
     * @var FeedFactory
     */
    private $_feedFactory;

    /**
     * @param BackendAuthSession $backendAuthSession
     * @param FeedFactory $feedFactory
     */
    public function __construct(
        BackendAuthSession $backendAuthSession,
        FeedFactory $feedFactory
    ) {
        $this->_feedFactory = $feedFactory;
        $this->_backendAuthSession = $backendAuthSession;
    }

    public function execute(Observer $observer)
    {
        if ($this->_backendAuthSession->isLoggedIn()) {
            $feedModel = $this->_feedFactory->create();
            /* @var $feedModel \RedChamps\Core\Model\Feed */
            $feedModel->checkUpdate();
        }
    }
}
