<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Order Number for Magento 2
 */

namespace Amasty\Number\Model\Number\Format;

use Amasty\Number\Model\ConfigProvider;
use Amasty\Number\Model\Number\AbstractFormatter;
use Amasty\Number\Model\SequenceStorage;
use Magento\Backend\Model\Session\Quote as QuoteBackendSession;
use Magento\Store\Model\StoreManagerInterface;

class StoreFormatter extends AbstractFormatter
{
    public const PLACEHOLDER = 'store';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var QuoteBackendSession|null
     */
    private $quoteBackendSession;

    public function __construct(
        ConfigProvider $configProvider,
        SequenceStorage $sequenceStorage,
        StoreManagerInterface $storeManager,
        QuoteBackendSession $quoteBackendSession = null
    ) {
        parent::__construct($configProvider, $sequenceStorage);
        $this->storeManager = $storeManager;
        $this->quoteBackendSession = $quoteBackendSession;
    }

    /**
     * @param string $template
     * @return string
     */
    public function format(string $template): string
    {
        $replacement = null;

        if ($this->quoteBackendSession) {
            $replacement = $this->quoteBackendSession->getStoreId();
        }

        if (!$replacement) {
            try {
                if ($this->getSequence()->getOrder()) {
                    $replacement = $this->getSequence()->getOrder()->getStoreId();
                } else {
                    $replacement = $this->storeManager->getStore()->getId();
                }
            } catch (\Exception $e) {
                $type = $this->getSequence()->getEntityType();
                $replacement = $this->getSequence()->getCounterScope($type)->getScopeId();
            }
        }

        return $this->replacePlaceholder($template, self::PLACEHOLDER, (string)$replacement);
    }
}
