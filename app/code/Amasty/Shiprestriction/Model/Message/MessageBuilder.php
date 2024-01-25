<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Model\Message;

use Amasty\Shiprestriction\Model\Message\MessageBuilderProcessors\MessageBuilderProcessorInterface;

class MessageBuilder
{
    /**
     * @var MessageBuilderProcessorInterface[]
     */
    private $messageBuilderProcessors;

    public function __construct(
        $messageBuilderProcessors = []
    ) {
        $this->initializeProcessors($messageBuilderProcessors);
    }

    /**
     * @param string $message
     * @param string[] $products @deprecated use \Amasty\Shiprestriction\Model\ProductRegistry::getProducts() instead
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function parseMessage(string $message, array $products = []): string
    {
        foreach ($this->messageBuilderProcessors as $processor) {
            $message = $processor->process($message);
        }

        return $message;
    }

    private function initializeProcessors(array $processors): void
    {
        foreach ($processors as $processor) {
            if (!$processor instanceof MessageBuilderProcessorInterface) {
                throw new \InvalidArgumentException(
                    'Type "' . get_class($processor) . '" is not instance of '
                    . MessageBuilderProcessorInterface::class
                );
            }
        }

        $this->messageBuilderProcessors = $processors;
    }
}
