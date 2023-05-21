<?php

namespace Balticode\CategoryConfigurator\Model;

use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\GuestCartManagementInterface;
use Magento\Quote\Api\GuestCartRepositoryInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Quote\Model\Quote;

class CurrentQuoteProvider
{
    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var GuestCartManagementInterface
     */
    protected $guestCartManagement;

    /**
     * @var GuestCartRepositoryInterface
     */
    protected $guestCartRepository;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @param HttpContext $httpContext
     * @param GuestCartManagementInterface $guestCartManagement
     * @param GuestCartRepositoryInterface $guestCartRepository
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        HttpContext $httpContext,
        GuestCartManagementInterface $guestCartManagement,
        GuestCartRepositoryInterface $guestCartRepository,
        CheckoutSession $checkoutSession
    ) {
        $this->httpContext = $httpContext;
        $this->guestCartManagement = $guestCartManagement;
        $this->guestCartRepository = $guestCartRepository;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return CartInterface|Quote|null
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function getCurrentQuote()
    {
        $currentQuote = $this->checkoutSession->getQuote();

        if (!$currentQuote->getId() && !$this->isLoggedIn()) {
            $currentQuote = $this->createGuestQuote();
            $this->checkoutSession->replaceQuote($currentQuote);
        }

        return $currentQuote;
    }

    /**
     * @return bool
     */
    protected function isLoggedIn()
    {
        return $this->httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }

    /**
     * @return CartInterface|null
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    protected function createGuestQuote()
    {
        $cartId = $this->guestCartManagement->createEmptyCart();

        return $this->guestCartRepository->get($cartId);
    }
}