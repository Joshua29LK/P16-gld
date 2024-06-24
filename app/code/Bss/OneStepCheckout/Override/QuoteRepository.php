<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category  BSS
 * @package   Bss_OneStepCheckout
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2023 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\OneStepCheckout\Override;

use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterfaceFactory;
use Magento\Quote\Api\Data\CartSearchResultsInterfaceFactory;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\ResourceModel\Quote\Collection as QuoteCollection;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class QuoteRepository extends \Magento\Quote\Model\QuoteRepository
{
    /**
     * @var \Bss\OneStepCheckout\Helper\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    /**
     * Construct
     *
     * @param \Bss\OneStepCheckout\Helper\Config $config
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param QuoteFactory $quoteFactory
     * @param StoreManagerInterface $storeManager
     * @param QuoteCollection $quoteCollection
     * @param CartSearchResultsInterfaceFactory $searchResultsDataFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param QuoteCollectionFactory $quoteCollectionFactory
     * @param CartInterfaceFactory $cartFactory
     */
    public function __construct(
        \Bss\OneStepCheckout\Helper\Config $config,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        QuoteFactory $quoteFactory,
        StoreManagerInterface $storeManager,
        QuoteCollection $quoteCollection,
        CartSearchResultsInterfaceFactory $searchResultsDataFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        QuoteCollectionFactory $quoteCollectionFactory,
        CartInterfaceFactory $cartFactory
    ) {
        $this->config = $config;
        $this->redirect = $redirect;
        parent::__construct(
            $quoteFactory,
            $storeManager,
            $quoteCollection,
            $searchResultsDataFactory,
            $extensionAttributesJoinProcessor,
            $collectionProcessor,
            $quoteCollectionFactory,
            $cartFactory
        );
    }

    /**
     * Get active quote by id, skip Bss_Osc check
     *
     * @param int $cartId
     * @param array $sharedStoreIds
     * @return \Magento\Quote\Api\Data\CartInterface|\Magento\Quote\Model\Quote|void
     * @throws NoSuchEntityException
     */
    public function getActive($cartId, array $sharedStoreIds = [])
    {
        if ($this->config->isEnabled()) {
            $quote = $this->get($cartId, $sharedStoreIds);
            $isOscCheckout = $this->checkUrlOSC();
            if (!$quote->getIsActive() && !$isOscCheckout) {
                throw NoSuchEntityException::singleField('cartId', $cartId);
            }
            return $quote;
        } else {
            parent::getActive($cartId, $sharedStoreIds);
        }
    }

    /**
     * Check is osc checkout
     *
     * @return bool
     */
    public function checkUrlOSC()
    {
        $referrerUrl = $this->redirect->getRefererUrl();
        $router = $this->config->getGeneral('router_name');
        if (!$router) {
            $router = 'onestepcheckout';
        }
        if (strpos($referrerUrl, $router) !== false) {
            return true;
        } else {
            return false;
        }
    }
}
