<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Archive for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Orderarchive\Block\Adminhtml\Customer;

use Amasty\Orderarchive\Controller\Adminhtml\Customer\Archive;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Ui\Component\Layout\Tabs\TabWrapper;

class ArchivedOrdersTab extends TabWrapper implements TabInterface
{
    /**
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var bool
     */
    protected $isAjaxLoaded = true;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    public function __construct(
        Context $context,
        Registry $registry,
        AuthorizationInterface $authorization,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->authorization = $authorization;

        parent::__construct($context, $data);
    }

    public function canShowTab(): bool
    {
        return (bool)$this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)
            && $this->authorization->isAllowed(Archive::ADMIN_RESOURCE);
    }

    public function getTabLabel(): Phrase
    {
        return __('Archived Orders');
    }

    public function getTabUrl(): string
    {
        return $this->getUrl('amastyorderarchive/customer/archive', ['_current' => true]);
    }
}
