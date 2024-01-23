<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Archive for Magento 2
 */

namespace Amasty\Orderarchive\Controller\Adminhtml\Customer;

use Magento\Customer\Controller\Adminhtml\Index;

class Archive extends Index
{
    public const ADMIN_RESOURCE = 'Amasty_Orderarchive::orderarchive_grid';

    public function execute()
    {
        $this->initCurrentCustomer();
        $resultLayout = $this->resultLayoutFactory->create();

        return $resultLayout;
    }
}
