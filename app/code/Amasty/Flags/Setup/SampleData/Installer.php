<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/
declare(strict_types=1);

namespace Amasty\Flags\Setup\SampleData;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Setup\SampleData\InstallerInterface;

class Installer implements InstallerInterface
{
    /**
     * @var State
     */
    private $appState;

    /**
     * @var InstallerInterface[]
     */
    private $installers;

    public function __construct(
        State $appState,
        array $installers = []
    ) {
        $this->appState = $appState;
        $this->installers = $installers;
    }

    public function install()
    {
        foreach ($this->installers as $installer) {
            $this->appState->emulateAreaCode(
                Area::AREA_ADMINHTML,
                [$installer, 'install']
            );
        }
    }
}
