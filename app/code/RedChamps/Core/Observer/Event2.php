<?php
namespace RedChamps\Core\Observer;

use Magento\Backend\Model\Auth\Session as BackendAuthSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Registry;
use RedChamps\Core\Model\FeedFactory;
use RedChamps\Core\Model\Processor;

/*
 * Package: Core
 * Class: Event2
 * Company: RedChamps
 * author: rav(rav@redchamps.com)
 * */
class Event2 implements ObserverInterface
{
    /**
     * @var BackendAuthSession
     */
    protected $_backendAuthSession;

    protected $moduleList;

    protected $processor;

    protected $registry;

    /**
     * @param FeedFactory $feedFactory
     * @param BackendAuthSession $backendAuthSession
     * @param ModuleListInterface $moduleList
     * @param Processor $processor
     * @param Registry $registry
     */
    public function __construct(
        BackendAuthSession $backendAuthSession,
        ModuleListInterface $moduleList,
        Processor $processor,
        Registry $registry
    ) {
        $this->moduleList = $moduleList;
        $this->registry = $registry;
        $this->processor = $processor;
        $this->_backendAuthSession = $backendAuthSession;
    }

    public function execute(Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $actionName = $request->getFullActionName();
        if (
            !$request->isAjax() &&
            $actionName != "redchamps_action_validate" &&
            $this->_backendAuthSession->isLoggedIn() &&
            ($this->processor->cF() || $this->processor->cHRF())
        ) {
            $extensionNames = $this->moduleList->getNames();
            $ourExtensions = $this->processor->filterExtensions($extensionNames);
            foreach ($ourExtensions as $extensionName) {
                if (!$this->registry->registry($extensionName . '_l_message')) {
                    $this->registry->register($extensionName . '_l_message', 1);
                    $this->processor->getExtensionVersion($extensionName, true);
                }
            }
        }
    }
}
