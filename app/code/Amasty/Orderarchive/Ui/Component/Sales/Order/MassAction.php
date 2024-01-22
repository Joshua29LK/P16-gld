<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Archive for Magento 2
 */

namespace Amasty\Orderarchive\Ui\Component\Sales\Order;

use Amasty\Orderarchive\Controller\Adminhtml\Customer\Archive;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\MassAction as MagentoMassAction;

class MassAction extends MagentoMassAction
{
    public const ACTION_COMPONENTS = [
        'add_to_archive',
        'delete_permanently'
    ];

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    public function __construct(
        ContextInterface $context,
        AuthorizationInterface $authorization,
        array $components = [],
        array $data = []
    ) {
        $this->authorization = $authorization;
        parent::__construct($context, $components, $data);
    }

    public function prepare(): void
    {
        if (!$this->authorization->isAllowed(Archive::ADMIN_RESOURCE)) {
            foreach (self::ACTION_COMPONENTS as $component) {
                $actionComponent = $this->getComponent($component);
                $componentConfig = $actionComponent->getConfiguration();
                $componentConfig['actionDisable'] = true;
                $actionComponent->setData('config', $componentConfig);
            }
        }

        parent::prepare();
    }
}
