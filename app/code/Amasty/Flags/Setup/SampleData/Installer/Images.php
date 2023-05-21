<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/
declare(strict_types=1);

namespace Amasty\Flags\Setup\SampleData\Installer;

use Amasty\Base\Helper\Deploy as DeployHelper;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Setup\SampleData\InstallerInterface;

class Images implements InstallerInterface
{
    public const DEPLOY_DIR = 'pub';

    /**
     * @var DeployHelper
     */
    private $deployHelper;

    /**
     * @var ComponentRegistrarInterface
     */
    private $componentRegistrar;

    public function __construct(
        DeployHelper $deployHelper,
        ComponentRegistrarInterface $componentRegistrar
    ) {
        $this->deployHelper = $deployHelper;
        $this->componentRegistrar = $componentRegistrar;
    }

    public function install()
    {
        $this->deployHelper->deployFolder(
            $this->componentRegistrar->getPath(
                ComponentRegistrar::MODULE,
                'Amasty_Flags'
            ) . DIRECTORY_SEPARATOR . self::DEPLOY_DIR
        );
    }
}
