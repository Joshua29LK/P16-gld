<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Conditions for Magento 2
 */

namespace Amasty\Conditions\Model\Negotiable;

class TotalsInformationManagement implements \Amasty\Conditions\Api\Negotiable\TotalsInformationManagementInterface
{
    /**
     * @var \Magento\Checkout\Api\TotalsInformationManagementInterface
     */
    private $originalInterface;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        \Magento\Checkout\Api\TotalsInformationManagementInterface $originalInterface,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->originalInterface = $originalInterface;
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritDoc}
     */
    public function calculate(
        $cartId,
        \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
    ) {
        /** @var \Magento\NegotiableQuote\Model\Webapi\CustomerCartValidator $validator */
        // @phpstan-ignore class.notFound
        $validator = $this->objectManager->create(\Magento\NegotiableQuote\Model\Webapi\CustomerCartValidator::class);
        $validator->validate($cartId);

        return $this->originalInterface->calculate($cartId, $addressInformation);
    }
}
