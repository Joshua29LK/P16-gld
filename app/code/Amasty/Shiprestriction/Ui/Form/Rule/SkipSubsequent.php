<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Ui\Form\Rule;

use Magento\Framework\Module\Manager;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Form\Field;

class SkipSubsequent extends Field
{
    /**
     * @var Manager
     */
    private $manager;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Manager $manager,
        $components,
        array $data = []
    ) {
        $this->manager = $manager;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepare()
    {
        $config = $this->getData('config');

        if (!$this->manager->isEnabled('Amasty_ShippingRestrictionSubscriptionFunctionality')) {
            $config['additionalInfo'] = '<span class="admin__field-note">The functionality is available '
                . 'as part of an active product subscription'
                . ' or support subscription. To upgrade and obtain functionality please follow the '
                . "<a href=https://amasty.com/amcustomer/account/products/?utm_source=extension"
                . "&utm_medium=backend&utm_campaign=subscribe_shippingrestrictions target='_blank'>link.</a>"
                . " Then you can find the 'amasty/module-shipping-restriction-subscription-functionality'"
                . " package for installation in composer suggest.</span>";

            $this->setData('config', $config);
        }

        parent::prepare();
    }
}
