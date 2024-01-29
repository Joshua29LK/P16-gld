<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Order Number for Magento 2
 */

namespace Amasty\Number\Model;

use Magento\Backend\Model\Session\Quote as QuoteBackendSession;
use Magento\Store\Model\StoreManagerInterface;

class ScopeResolver
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var QuoteBackendSession
     */
    private $quoteBackendSession;

    public function __construct(
        StoreManagerInterface $storeManager,
        QuoteBackendSession $quoteBackendSession = null
    ) {
        $this->storeManager = $storeManager;
        $this->quoteBackendSession = $quoteBackendSession;
    }

    public function getStoreId(): int
    {
        $storeId = null;

        if ($this->quoteBackendSession) {
            $storeId = (int)$this->quoteBackendSession->getStoreId();
        }

        return $storeId ?: (int)$this->storeManager->getStore()->getId();
    }
}
