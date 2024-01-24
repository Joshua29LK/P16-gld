<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Model\Command\Pdf\PdfCollector;

interface PdfCollectorInterface
{
    /**
     * @param \Zend_Pdf[] $pdfs
     * @return PdfCollectorInterface
     */
    public function collect(array $pdfs): PdfCollectorInterface;

    public function getExtension(): string;

    public function render(): string;

    public function hasResponse(): bool;

    public function useSeparateFiles(): bool;

    public function isAvailable(): bool;
}
