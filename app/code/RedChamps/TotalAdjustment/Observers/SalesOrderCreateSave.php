<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Observers;

use Magento\Backend\Model\Session\Quote;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface as ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use RedChamps\TotalAdjustment\Model\AdjustmentManager;
use RedChamps\TotalAdjustment\Model\ConfigReader;

class SalesOrderCreateSave implements ObserverInterface
{
    /**
     * @var Quote
     */
    protected $backendSessionQuote;

    protected $messageManager;

    protected $responseFactory;

    protected $redirect;

    protected $configReader;

    protected $adjustmentManager;

    /**
     * SalesOrderCreateSave constructor.
     * @param Quote $backendSessionQuote
     * @param ManagerInterface $messageManager
     * @param ResponseFactory $responseFactory
     * @param RedirectInterface $redirect
     * @param ConfigReader $configReader
     */
    public function __construct(
        Quote $backendSessionQuote,
        ManagerInterface $messageManager,
        ResponseFactory $responseFactory,
        RedirectInterface $redirect,
        ConfigReader $configReader,
        AdjustmentManager $adjustmentManager
    ) {
        $this->backendSessionQuote = $backendSessionQuote;
        $this->messageManager = $messageManager;
        $this->responseFactory = $responseFactory;
        $this->redirect = $redirect;
        $this->configReader = $configReader;
        $this->adjustmentManager =$adjustmentManager;
    }

    public function execute(Observer $observer)
    {
        $quote = $this->backendSessionQuote->getQuote();
        foreach ($quote->getAllAddresses() as $address) {
            if ($address->getAddressType() == 'shipping') {
                $adjustmentAmounts = $observer->getRequest()->getPost('adjustment_amount');
                $adjustmentPercentageAmounts = $observer->getRequest()->getPost('adjustment_percentage_amount');
                $adjustmentTitles = $observer->getRequest()->getPost('adjustment_title');
                $adjustmentTypes = $observer->getRequest()->getPost('adjustment_type');
                $adjustments = [];
                for ($i=0;$i<count($adjustmentTitles);$i++) {
                    if ($adjustmentTitles[$i]) {
                        $isPercentage = $adjustmentTypes[$i] == "percentage";
                        $amount = $isPercentage ?
                            $adjustmentPercentageAmounts[$i] :
                            $adjustmentAmounts[$i];
                        $adjustment = [
                            "title" => $adjustmentTitles[$i],
                            "type" => $adjustmentTypes[$i],
                            "amount" => $amount
                        ];
                        $adjustment['base_amount'] = $this->configReader->convertToBaseCurrency($amount, $quote->getStoreId());
                        if ($isPercentage) {
                            $adjustment['percentage'] = $adjustmentAmounts[$i];
                        }
                        $adjustments[] = $adjustment;
                    }
                }
                if (count($adjustments)) {
                    $adjustments = $this->adjustmentManager->encodeAdjustments($adjustments);
                    $address->setAdjustments($adjustments);
                    $quote->setAdjustments($adjustments);
                }
                //validate uniqueness of adjustment titles
                if (count($adjustmentTitles) && count($adjustmentTitles) != count(array_unique($adjustmentTitles))) {
                    $address->getResource()->save($address);
                    $quote->getResource()->save($quote);
                    $this->messageManager->addErrorMessage(
                        __("Validation Failed: Multiple adjustments exist with same title. Please modify the titles and retry.")
                    );
                    $this->responseFactory->create()->setRedirect($this->redirect->getRefererUrl())->sendResponse();
                    //Die statement is knowinigly written, in order to redirect from the observer
                    die();
                }
            }
        }
    }
}
