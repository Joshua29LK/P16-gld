<?php
namespace Hoofdfabriek\BePostcodeNL\Observer;

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
            if (isset($accountInfo['countries']) && !array_search('BEL', $accountInfo['countries'])) {
                $this->config->setConfigValue('postcodenl/autocomplete/use_be_autocomplete', 0);
                $this->messageManager->addErrorMessage(
                    __('Belgium API is not enabled in PostcodeNL.'),
                    $this->messageManager->getDefaultGroup()
                );
            }
        }
    }
}
