<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Model\Sales\Quote;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\QuoteRepository;
use RedChamps\TotalAdjustment\Api\Quote\AdjustmentsInterface;
use RedChamps\TotalAdjustment\Model\AdjustmentModifier\Actions;

class AdjustmentsModifier implements AdjustmentsInterface
{
    protected $addActionProcessor;

    protected $removeActionProcessor;

    protected $editActionProcessor;

    protected $_quote;

    protected $repository;

    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * AdjustmentsModifier constructor.
     * @param Actions\Add $addAction
     * @param Actions\Remove $removeAction
     * @param Actions\Edit $editAction
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(
        Json $serializer,
        Actions\Add $addAction,
        Actions\Remove $removeAction,
        Actions\Edit $editAction,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        QuoteRepository $quoteRepository
    ) {
        $this->addActionProcessor = $addAction;
        $this->removeActionProcessor = $removeAction;
        $this->editActionProcessor = $editAction;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->repository = $quoteRepository;
        $this->serializer = $serializer;
    }

    /**
     * @param string $cartId
     * @param mixed $adjustments
     * @param bool $nonApi
     * @return false|\Magento\Framework\Phrase|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addAdjustments($cartId, $adjustments, $nonApi = false)
    {
        $quote = $this->getQuote($cartId);
        $result =  $this->addActionProcessor->execute($quote, $adjustments);
        return $this->postProcess($quote, $result, $nonApi);
    }

    /**
     * @param int $cartId
     * @param mixed $adjustmentTitles
     * @param bool $nonApi
     * @return false|\Magento\Framework\Phrase|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function removeAdjustments($cartId, $adjustmentTitles, $nonApi = false)
    {
        $quote = $this->getQuote($cartId);
        $result = $this->removeActionProcessor->execute($quote, $adjustmentTitles);
        return $this->postProcess($quote, $result, $nonApi);
    }

    /**
     * @param int $cartId
     * @param mixed $adjustments
     * @param bool $nonApi
     * @return false|\Magento\Framework\Phrase|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function editAdjustments($cartId, $adjustments, $nonApi = false)
    {
        $quote = $this->getQuote($cartId);
        $result = $this->editActionProcessor->execute($quote, $adjustments);
        return $this->postProcess($quote, $result, $nonApi);
    }

    protected function getQuote($cartId)
    {
        if (!$this->_quote) {
            if (!is_numeric($cartId)) {
                $quoteMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
                $cartId = $quoteMask->getQuoteId();
            }
            $quote = $this->repository->get($cartId);
            if (!$quote || !$quote->getId()) {
                throw new LocalizedException(__("No quote exist with specified ID."));
            }
            $this->_quote = $quote;
        }
        return $this->_quote;
    }

    /**
     * @param CartInterface $quote
     * @param $result
     * @param $nonApi
     * @return false|string
     */
    protected function postProcess($quote, $result, $nonApi)
    {
        $quote->getShippingAddress()->setAdjustments($quote->getAdjustments());
        $quote->collectTotals();
        $this->repository->save($quote);
        if ($nonApi) {
            return $result;
        }
        return $this->formatResponse($result, $quote);
    }

    protected function formatResponse($response, $quote)
    {
        $result = [
            'message' => $response,
            "new_grand_total" => $quote->getGrandTotal(),
            "new_base_grand_total" => $quote->getBaseGrandTotal()
        ];
        return $this->serializer->serialize($result);
    }
}
