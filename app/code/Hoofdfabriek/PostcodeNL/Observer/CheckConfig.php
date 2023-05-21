<?php
namespace Hoofdfabriek\PostcodeNL\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Hoofdfabriek\PostcodeNL\Model\Config;
use Magento\Framework\Message\ManagerInterface;
use Hoofdfabriek\PostcodeNL\Model\PostcodeNL;

class CheckConfig implements ObserverInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var PostcodeNL
     */
    protected $postcodeNLApi;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * CheckConfig constructor.
     * @param Config $config
     * @param PostcodeNL $postcodeNL
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Config $config,
        PostcodeNL $postcodeNL,
        ManagerInterface $messageManager
    ) {
        $this->config = $config;
        $this->postcodeNLApi = $postcodeNL;
        $this->messageManager = $messageManager;
    }

    /**
     * Check if postcodeNL creds are valid
     *
     * @param Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Observer $observer)
    {
        if ($this->config->isPostcodeNLEnabled()) {
            $accountInfo = $this->postcodeNLApi->getAccountInfo();
            if (!$accountInfo) {
                $this->config->disableModule();
                $this->messageManager->addErrorMessage(
                    __('Please enter proper API creds.'),
                    $this->messageManager->getDefaultGroup()
                );
            } elseif (isset($accountInfo['message'])) {
                $this->config->disableModule();
                $this->messageManager->addErrorMessage(
                    $accountInfo['message'],
                    $this->messageManager->getDefaultGroup()
                );
            } elseif (isset($accountInfo['hasAccess']) && $accountInfo['hasAccess']) {
                $this->messageManager->addSuccessMessage(
                    __('PostcodeNL Account %1 is verified', $accountInfo['name']),
                    $this->messageManager->getDefaultGroup()
                );
            } elseif (isset($accountInfo['hasAccess']) && !$accountInfo['hasAccess']) {
                $this->config->disableModule();
                $this->messageManager->addErrorMessage(
                    __('PostcodeNL Account %1 is verified but doesn\'t have access', $accountInfo['name']),
                    $this->messageManager->getDefaultGroup()
                );
            }
        }
    }
}
