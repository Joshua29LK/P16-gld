<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Order Number for Magento 2
 */

namespace Amasty\Number\Setup\Patch\Data;

use Amasty\Number\Setup\Operation\MoveDataFromConfig;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpgradeTo200 implements DataPatchInterface
{
    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * @var MoveDataFromConfig
     */
    private $moveDataFromConfig;

    /**
     * @var State
     */
    private $appState;

    public function __construct(
        ResourceInterface $moduleResource,
        MoveDataFromConfig $moveDataFromConfig,
        State $appState
    ) {
        $this->moduleResource = $moduleResource;
        $this->moveDataFromConfig = $moveDataFromConfig;
        $this->appState = $appState;
    }

    public function apply()
    {
        $setupDataVersion = $this->moduleResource->getDataVersion('Amasty_Rma');

        // Check if module was already installed or not.
        // If setup_version present in DB then we don't need to install fixtures, because setup_version is a marker.
        if (!$setupDataVersion || version_compare($setupDataVersion, '2.0.0', '<')) {
            $this->appState->emulateAreaCode(
                Area::AREA_ADMINHTML,
                [$this->moveDataFromConfig, 'execute']
            );
        }
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
