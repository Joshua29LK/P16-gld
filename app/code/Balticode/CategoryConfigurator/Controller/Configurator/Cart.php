<?php

namespace Balticode\CategoryConfigurator\Controller\Configurator;

use Balticode\CategoryConfigurator\Model\CurrentQuoteProvider;
use Balticode\CategoryConfigurator\Model\QuoteManager;
use Balticode\CategoryConfigurator\Model\Validator\ValidatorInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\GuestCartRepositoryInterface;
use Magento\Quote\Model\QuoteRepository;
use Zend_Json;
use Exception;

class Cart extends Action
{
    const ERROR_MESSAGE_FAILED_REQUEST = 'An error occurred while handling your request.';
    const ERROR_MESSAGE_INCORRECT_PARAMETERS = 'Provided request parameters were incorrect.';
    const ERROR_MESSAGE_PRODUCT_NOT_FOUND = 'The product you are looking for could not have been found.';
    const ERROR_MESSAGE_CANT_ADD_ITEM = 'We can\'t add this item to your shopping cart right now.';

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var GuestCartRepositoryInterface
     */
    protected $guestCartRepository;

    /**
     * @var QuoteManager
     */
    protected $quoteManager;

    /**
     * @var CurrentQuoteProvider
     */
    protected $currentQuoteProvider;

    /**
     * @param Context $context
     * @param ValidatorInterface $validator
     * @param QuoteRepository $quoteRepository
     * @param GuestCartRepositoryInterface $guestCartRepository
     * @param QuoteManager $quoteManager
     * @param CurrentQuoteProvider $currentQuoteProvider
     */
    public function __construct(
        Context $context,
        ValidatorInterface $validator,
        QuoteRepository $quoteRepository,
        GuestCartRepositoryInterface $guestCartRepository,
        QuoteManager $quoteManager,
        CurrentQuoteProvider $currentQuoteProvider
    ) {
        $this->validator = $validator;
        $this->quoteRepository = $quoteRepository;
        $this->guestCartRepository = $guestCartRepository;
        $this->quoteManager = $quoteManager;
        $this->currentQuoteProvider = $currentQuoteProvider;

        parent::__construct($context);
    }

    /**
     * @return void
     */
    public function execute()
    {
        $checkoutPageUrl = $this->_url->getUrl('checkout/cart', ['_secure' => true]);

        $data = [
            'redirectUrl' => $checkoutPageUrl,
        ];

        try {
            $this->processAddToCartRequest();
        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage(__($exception->getMessage()));
            $data['error'] = 1;
        }

        $this->getResponse()->representJson(Zend_Json::encode($data));
    }

    /**
     * @throws CouldNotSaveException
     * @throws Exception
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    protected function processAddToCartRequest()
    {
        if (!$this->getRequest()->isAjax()) {
            throw new Exception(self::ERROR_MESSAGE_FAILED_REQUEST);
        }

        $stepsData = (array) $this->getRequest()->getPost();

        if (!$this->validator->validate($stepsData)) {
            throw new Exception(self::ERROR_MESSAGE_INCORRECT_PARAMETERS);
        }

        $currentQuote = $this->currentQuoteProvider->getCurrentQuote();

        foreach ($stepsData as $selectedProducts) {
            $this->quoteManager->addStepProductsToQuote($currentQuote, $selectedProducts);
        }

        $this->quoteRepository->save($currentQuote);
    }
}