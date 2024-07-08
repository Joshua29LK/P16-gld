<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CustomShippingPrice\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
class Config extends AbstractHelper
{
    /**
     * Get carrier settings
     *
     * @param $key
     * @return mixed
     */
    public function getConfig($key)
    {
        return $this->scopeConfig->getValue('carriers/custom_shipping/'.$key);
    }

    /**
     * Get module settings
     *
     * @param $key
     * @return mixed
     */
    public function getConfigModule($key)
    {
        return $this->scopeConfig
            ->getValue('mageside_customshippingprice/general/' . $key);
    }
}
