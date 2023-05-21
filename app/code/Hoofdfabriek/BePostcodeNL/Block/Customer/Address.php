<?php
namespace Hoofdfabriek\BePostcodeNL\Block\Customer;

use Magento\Store\Model\ScopeInterface;

/**
 * Class Address
 */
class Address extends \Hoofdfabriek\PostcodeNL\Block\Customer\Address
{
    /**
     * Check if BE autocomplete enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_scopeConfig->isSetFlag('postcodenl/autocomplete/use_be_autocomplete', ScopeInterface::SCOPE_STORE);
    }
}
