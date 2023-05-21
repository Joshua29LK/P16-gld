<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Model\Action;

use Amasty\Oaction\Model\Action\Modifier\ActionModifierInterface;

class ActionModifierProvider
{
    /**
     * @var ActionModifierInterface[]
     */
    private $modifiers = [];

    public function __construct(
        array $modifiers = []
    ) {
        $this->initializeModifiers($modifiers);
    }

    /**
     * @param string $actionName
     * @return ActionModifierInterface
     */
    public function get(string $actionName): ?ActionModifierInterface
    {
        return $this->modifiers[$actionName] ?? null;
    }

    private function initializeModifiers(array $modifiers): void
    {
        foreach ($modifiers as $actionName => $modifier) {
            if (!$modifier instanceof ActionModifierInterface) {
                throw new \LogicException(
                    sprintf('Modifier must implement %s', ActionModifierInterface::class)
                );
            }

            $this->modifiers[$actionName] = $modifier;
        }
    }
}
