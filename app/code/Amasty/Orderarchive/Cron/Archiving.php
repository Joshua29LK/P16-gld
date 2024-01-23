<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Archive for Magento 2
 */

namespace Amasty\Orderarchive\Cron;

use Amasty\Orderarchive\Api\ArchiveProcessorInterface;
use Amasty\Orderarchive\Helper;
use Amasty\Orderarchive\Model\Source\Frequency;

/**
 * custom cron actions
 */
class Archiving
{
    /**
     * @var Helper\Data
     */
    protected $helper;

    /**
     * @var Helper\Email\Data
     */
    private $emailHelper;

    /**
     * @var ArchiveProcessorInterface
     */
    private $orderProcessor;

    public function __construct(
        Helper\Data $helper,
        Helper\Email\Data $emailHelper,
        ArchiveProcessorInterface $orderProcessor
    ) {
        $this->helper = $helper;
        $this->emailHelper = $emailHelper;
        $this->orderProcessor = $orderProcessor;
    }

    public function execute(): void
    {
        if ($this->helper->isModuleOn()) {
            $result = $this->orderProcessor->moveAllToArchive();

            if ((int)$result->getOrderIds() > 0) {
                $this->emailHelper->orderArchivedAfter($result);
            }
        }
    }
}
