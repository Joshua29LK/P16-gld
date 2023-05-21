<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_DependentCustomOption
 * @author     Extension Team
 * @copyright  Copyright (c) 2020-2021 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\DependentCustomOption\Model;

use Bss\DependentCustomOption\Api\Data\DependentOptionConfigInterface;
use Bss\DependentCustomOption\Api\Data\DependentOptionConfigInterfaceFactory;
use Bss\DependentCustomOption\Api\DependentOptionManagementInterface;
use Bss\DependentCustomOption\Helper\ModuleConfig;

class DependentOptionManagement implements DependentOptionManagementInterface
{
    /**
     * @var ModuleConfig
     */
    protected $moduleConfig;

    /**
     * @var DependentOptionConfigInterfaceFactory
     */
    protected $configInterfaceFactory;

    /**
     * DependentOptionManagement constructor.
     * @param ModuleConfig $moduleConfig
     * @param DependentOptionConfigInterfaceFactory $configInterfaceFactory
     */
    public function __construct(
        ModuleConfig $moduleConfig,
        DependentOptionConfigInterfaceFactory $configInterfaceFactory
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->configInterfaceFactory = $configInterfaceFactory;
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        return $this->getDependCoConfig();
    }

    /**
     * @inheritDoc
     */
    public function getDependCoConfig()
    {
        $enableStatus = $this->moduleConfig->isModuleEnable();
        $childValues = $this->moduleConfig->getChildrenDisplay();
        $multiParent = $this->moduleConfig->getMultipleParentValue();

        /** @var DependentOptionConfigInterface $dependentOptionConfig */
        $dependentOptionConfig = $this->configInterfaceFactory->create();
        $dependentOptionConfig->addData([
            DependentOptionConfigInterface::ENABLE => $enableStatus,
            DependentOptionConfigInterface::CHILDREN_DISPLAY => $childValues,
            DependentOptionConfigInterface::MULTIPLE_PARENT => $multiParent,
        ]);

        return $dependentOptionConfig;
    }
}
