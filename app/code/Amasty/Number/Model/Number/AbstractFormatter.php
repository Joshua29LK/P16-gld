<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Order Number for Magento 2
 */

namespace Amasty\Number\Model\Number;

use Amasty\Number\Model\ConfigProvider;
use Amasty\Number\Model\SequenceStorage;

abstract class AbstractFormatter
{
    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * @var SequenceStorage
     */
    private $sequenceStorage;

    public function __construct(
        ConfigProvider $configProvider,
        SequenceStorage $sequenceStorage
    ) {
        $this->configProvider = $configProvider;
        $this->sequenceStorage = $sequenceStorage;
    }

    /**
     * @return SequenceStorage
     */
    public function getSequence()
    {
        return $this->sequenceStorage;
    }

    /**
     * @param string $template
     * @param string $placeholder
     * @param string $replacement
     * @return string
     */
    public function replacePlaceholder(string $template, string $placeholder, string $replacement): string
    {
        return str_replace('{' . $placeholder . '}', $replacement, $template);
    }

    abstract public function format(string $template): string;
}
