<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Model\Command\Pdf\PdfCollector;

class DefaultCollector implements PdfCollectorInterface
{
    /**
     * @var \Zend_Pdf|null
     */
    private $pdf = null;

    /**
     * @param \Zend_Pdf[] $pdfs
     * @return PdfCollectorInterface
     */
    public function collect(array $pdfs): PdfCollectorInterface
    {
        $this->pdf = array_shift($pdfs);

        if ($this->hasPdf()) {
            /** @var \Zend_Pdf $pdf */
            foreach ($pdfs as $pdf) {
                // TODO: Use \Zend_Pdf_Resource_Extractor::clonePage()
                //  because using a clone increases the size of the merged file
                // phpcs:ignore Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
                $this->pdf->pages = array_merge($this->pdf->pages, $pdf->pages);
            }
        }

        return $this;
    }

    public function getExtension(): string
    {
        return 'pdf';
    }

    public function render(): string
    {
        return $this->hasPdf() ? $this->pdf->render() : '';
    }

    public function hasResponse(): bool
    {
        return $this->hasPdf() && count($this->pdf->pages);
    }

    public function useSeparateFiles(): bool
    {
        return false;
    }

    public function isAvailable(): bool
    {
        return true;
    }

    private function hasPdf(): bool
    {
        return !empty($this->pdf);
    }
}
