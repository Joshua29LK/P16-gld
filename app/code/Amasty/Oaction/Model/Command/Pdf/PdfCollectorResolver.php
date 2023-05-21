<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Model\Command\Pdf;

use Amasty\Oaction\Model\Command\Pdf\PdfCollector\DefaultCollector;
use Amasty\Oaction\Model\Command\Pdf\PdfCollector\PdfCollectorInterface;

class PdfCollectorResolver
{
    /**
     * @var DefaultCollector
     */
    private $defaultCollector;

    /**
     * @var PdfCollectorInterface|null
     */
    private $collector = null;

    public function __construct(
        DefaultCollector $defaultCollector,
        ?PdfCollectorInterface $collector = null
    ) {
        $this->defaultCollector = $defaultCollector;
        $this->initializeCollector($collector);
    }

    public function get(): PdfCollectorInterface
    {
        return $this->collector ?? $this->defaultCollector;
    }

    private function initializeCollector(?PdfCollectorInterface $collector): void
    {
        $this->collector = $collector && $collector->isAvailable()
            ? $collector
            : null;
    }
}
