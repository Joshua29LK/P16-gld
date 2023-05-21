<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Custom Checkout Fields for Magento 2
 */

namespace Amasty\Orderattr\Model\Webapi;

/**
 * Replaces a "%amasty_cart_id%" value with the current authenticated customer's cart
 */
class ParamOverriderAmastyCartId  extends \Magento\Quote\Model\Webapi\ParamOverriderCartId
{

}
