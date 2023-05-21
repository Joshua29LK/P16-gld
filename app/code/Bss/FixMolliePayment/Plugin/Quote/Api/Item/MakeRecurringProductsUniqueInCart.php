<?php
namespace Bss\FixMolliePayment\Plugin\Quote\Api\Item;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Quote\Api\Data\CartItemInterface;

class MakeRecurringProductsUniqueInCart extends \Mollie\Payment\Plugin\Quote\Api\Item\MakeRecurringProductsUniqueInCart
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
        parent::__construct($serializer);
    }

    /**
     * @param CartItemInterface $item
     * @param bool $result
     * @return bool
     */
    public function afterRepresentProduct(CartItemInterface $item, bool $result): bool
    {
        $buyRequest = $item->getOptionByCode('info_buyRequest');
        if ($buyRequest && ((
                strstr($buyRequest->getValue(), 'is_recurring') !== false &&
                $this->jsonContainsRecurringValue($buyRequest->getValue())
            ) ||
            strstr($buyRequest->getValue(), 'purchase') !== false
        )) {
            return false;
        }

        return $result;
    }

    /**
     * @param string $json
     * @return bool
     */
    private function jsonContainsRecurringValue(string $json): bool
    {
        $data = $this->serializer->unserialize($json);

        return isset($data['mollie_metadata'], $data['mollie_metadata']['is_recurring']) &&
            $data['mollie_metadata']['is_recurring'] == 1;
    }
}
