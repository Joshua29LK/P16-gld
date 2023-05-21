<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Block\Adminhtml\Sales\Order\Total;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Session\Quote;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Block\Adminhtml\Order\Create\Totals\DefaultTotals;
use Magento\Sales\Helper\Data;
use Magento\Sales\Model\AdminOrder\Create;
use Magento\Sales\Model\Config;
use RedChamps\TotalAdjustment\Model\AdjustmentManager;
use RedChamps\TotalAdjustment\Model\ConfigReader;

class Adjustment extends DefaultTotals
{
    protected $_template = 'RedChamps_TotalAdjustment::sales/order/create/totals/adjustment.phtml';

    /**
     * @var AdjustmentManager
     */
    private $adjustmentManager;

    /**
     * @var ConfigReader
     */
    private $configReader;

    public function __construct(
        AdjustmentManager $adjustmentManager,
        ConfigReader $configReader,
        Context $context,
        Quote $sessionQuote,
        Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        Data $salesData,
        Config $salesConfig,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $sessionQuote,
            $orderCreate,
            $priceCurrency,
            $salesData,
            $salesConfig,
            $data
        );
        $this->adjustmentManager = $adjustmentManager;
        $this->configReader = $configReader;
    }

    public function getAdjustments()
    {
        $adjustments = $this->getQuote()->getAdjustments();
        if ($adjustments) {
            return $this->decodeAdjustments($adjustments);
        }
        return [];
    }

    public function getConfigReader()
    {
        return $this->configReader;
    }

    public function decodeAdjustments($adjustments)
    {
        return $this->adjustmentManager->decodeAdjustments($adjustments);
    }
}
