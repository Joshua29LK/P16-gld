<?php
namespace RedChamps\Core\Cron;

use Magento\Framework\Module\ModuleListInterface;
use RedChamps\Core\Model\FeedFactory;
use RedChamps\Core\Model\Processor;

class GetUpdates
{

    protected $moduleList;

    protected $processor;

    public function __construct(
        ModuleListInterface $moduleList,
        Processor $processor
    ) {
        $this->moduleList = $moduleList;
        $this->processor = $processor;
    }
    public function execute($force = false)
    {
        if ($force || $this->processor->canRun()) {
            $extensionNames = $this->moduleList->getNames();
            $ourExtensions = $this->processor->filterExtensions($extensionNames);
            $this->processor->prepareExtensionVersions($ourExtensions);
        }
    }
}
