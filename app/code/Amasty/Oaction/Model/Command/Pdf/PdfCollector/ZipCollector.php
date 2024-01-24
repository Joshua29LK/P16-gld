<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Model\Command\Pdf\PdfCollector;

use Amasty\PDFCustom\Model\ConfigProvider;
use Amasty\PDFCustom\Model\Zip;
use Amasty\PDFCustom\Model\ZipFactory;
use Magento\Framework\Module\Manager;
use Magento\Framework\ObjectManagerInterface;

class ZipCollector implements PdfCollectorInterface
{
    public const AMASTY_PDF_CUSTOMIZER_MODULE_NAME = 'Amasty_PDFCustom';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var DefaultCollector
     */
    private $defaultCollector;

    /**
     * @var Zip|null
     */
    private $zip = null;

    public function __construct(
        ObjectManagerInterface $objectManager,
        Manager $moduleManager,
        DefaultCollector $defaultCollector
    ) {
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
        $this->defaultCollector = $defaultCollector;
        $this->initializeZip();
    }

    /**
     * @param \Zend_Pdf[] $pdfs
     * @return PdfCollectorInterface
     */
    public function collect(array $pdfs): PdfCollectorInterface
    {
        if ((count($pdfs) < 2) || !$this->hasZip()) {
            return $this->defaultCollector->collect($pdfs);
        }

        /** @var \Zend_Pdf $pdf */
        foreach ($pdfs as $name => $pdf) {
            $this->zip->addFileFromString(
                sprintf('packingslip_%s.pdf', $name),
                $pdf->render()
            );
        }

        return $this;
    }

    public function getExtension(): string
    {
        return 'zip';
    }

    public function render(): string
    {
        return $this->hasZip() ? $this->zip->render() : '';
    }

    public function hasResponse(): bool
    {
        return $this->hasZip() && $this->zip->countFiles();
    }

    public function useSeparateFiles(): bool
    {
        return true;
    }

    public function isAvailable(): bool
    {
        return $this->moduleManager->isEnabled(self::AMASTY_PDF_CUSTOMIZER_MODULE_NAME)
            && $this->hasZip();
    }

    private function hasZip(): bool
    {
        return !empty($this->zip);
    }

    private function initializeZip(): void
    {
        if (class_exists(Zip::class)
            && (!class_exists(ConfigProvider::class)
                || $this->objectManager->get(ConfigProvider::class)->isEnabled()
            )
        ) {
            $this->zip = $this->objectManager->get(ZipFactory::class)->create();
        }
    }
}
