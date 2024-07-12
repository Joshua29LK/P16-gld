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
 * @package   Bss_CheckoutMsg
 * @author    Extension Team
 * @copyright Copyright (c) 2024 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CheckoutMsg\Block\Checkout;

use Magento\Checkout\Model\Session as SessionModel;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Style
 * @package Bss\OneStepCheckout\Block
 */
class CustomMessage extends Template
{
    /**
     * @var SessionModel
     */
    protected $checkoutSession;

    private const SHIPPING_GROUP_ATT = "verzendgroep";

    private const SHIPPING_GROUP_VALUE = 4179;

    private const SHIPPING_GROUP_MESSAGE = "Een of meerdere producten in uw winkelwagentje komen niet in aanmerking voor spoedlevering.";

    /**
     * Style constructor.
     * @param Context $context
     * @param \Bss\OneStepCheckout\Helper\Config $helperConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        SessionModel $checkoutSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return string
     */
    public function getCustomMessage()
    {
        $quote = $this->checkoutSession->getQuote();
        $isUrgent = $isNotUrgent = 0;
        foreach ($quote->getItems() as $item) {
            $shippingGroup = $item->getProduct()->getData(self::SHIPPING_GROUP_ATT);
            if($shippingGroup == self::SHIPPING_GROUP_VALUE) {
                $isUrgent = 1;
            } else {
                $isNotUrgent = 1;
            }
            if ($isUrgent == 1 && $isNotUrgent == 1) {
                return self::SHIPPING_GROUP_MESSAGE;
            }
        }
        return "";
    }

}
