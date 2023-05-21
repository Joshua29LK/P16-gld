<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Model\Source;

use Amasty\Oaction\Model\Action\Modifier\OrderAttributesModifier;
use Amasty\Oaction\Model\Action\Modifier\PrintPackingSlipsModifier;
use Amasty\Oaction\Model\OrderAttributesChecker;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Serialize\Serializer\Json;

class Commands implements OptionSourceInterface
{
    public const COMPOSER_FILE = 'composer.json';

    /**
     * @var OrderAttributesChecker
     */
    private $orderAttributesChecker;

    /**
     * @var Reader
     */
    private $moduleReader;

    /**
     * @var File
     */
    private $filesystem;

    /**
     * @var Json
     */
    private $jsonSerializer;

    public function __construct(
        OrderAttributesChecker $orderAttributesChecker,
        Reader $moduleReader,
        File $filesystem,
        Json $jsonSerializer
    ) {
        $this->orderAttributesChecker = $orderAttributesChecker;
        $this->moduleReader = $moduleReader;
        $this->filesystem = $filesystem;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];
        $types = [
            '' => '',
            'amasty_oaction_invoice' => __('Invoice'),
            'amasty_oaction_invoiceship' => __('Invoice > Ship'),
            'amasty_oaction_ship' => __('Ship'),
            'amasty_oaction_status' => __('Change Status'),
            'amasty_oaction_statusnotify' => __('Change Status and Notify'),
            'amasty_oaction_comment' => __('Add Comment'),
            'amasty_oaction_sendtrack' => __('Send Tracking Information'),
            PrintPackingSlipsModifier::ACTION_PRINT_PACKING_SLIPS => __('Print Packing Slips')
        ];

        if ($this->orderAttributesChecker->isModuleExist(false)
            && $this->getVersion() > '2.1.6'
        ) {
            $types[OrderAttributesModifier::ACTION_ORDER_ATTRIBUTES] = __('Update Order Attributes');
        }

        foreach ($types as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $options;
    }

    /**
     * @return string
     */
    private function getVersion(): string
    {
        $version = '';
        $info = $this->getModuleInfo();

        if (isset($info['version'])) {
            $version = $info['version'];
        }

        return $version;
    }

    /**
     * @return array
     */
    private function getModuleInfo(): array
    {
        $json = [];

        try {
            $dir = $this->moduleReader->getModuleDir(
                '',
                OrderAttributesChecker::AMASTY_ORDER_ATTRIBUTES_MODULE_NAME
            );
            $file = $dir . '/' . self::COMPOSER_FILE;
            $string = $this->filesystem->fileGetContents($file);
            $json = $this->jsonSerializer->unserialize($string);
        } catch (FileSystemException $e) {
            null;
        }

        return $json;
    }
}
