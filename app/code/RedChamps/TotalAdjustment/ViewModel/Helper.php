<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use RedChamps\TotalAdjustment\Model\ConfigReader;
use RedChamps\TotalAdjustment\Model\AdjustmentManager;

class Helper implements ArgumentInterface
{
    protected $configReader;

    protected $adjustmentManager;

    public function __construct(
        ConfigReader $configReader,
        AdjustmentManager $adjustmentManager
    ) {
        $this->configReader = $configReader;
        $this->adjustmentManager = $adjustmentManager;
    }

    public function getConfigReader()
    {
        return $this->configReader;
    }

    public function getAdjustmentManager()
    {
        return $this->adjustmentManager;
    }
}
