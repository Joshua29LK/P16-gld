<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Checkout Fields for Magento 2
 */

namespace Amasty\Orderattr\Block\Adminhtml\Order\Plugin;

use Amasty\Orderattr\Block\Adminhtml\Order\Create\Form\Attributes;

class CreateFormOrderAttributes
{
    public function afterToHtml(\Magento\Sales\Block\Adminhtml\Order\Create\Form\Account $subject, $result)
    {
        $orderAttributesForm = $subject->getLayout()->createBlock(
            Attributes::class,
            '',
            ['orderStoreId' => $subject->getStore()->getId()]
        );
        $orderAttributesForm->setQuote($subject->getQuote());

        return $result . $orderAttributesForm->toHtml();
    }
}
