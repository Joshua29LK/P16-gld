<?php
namespace RedChamps\ShareCart\Controller\Action;

class Restore extends Base
{
    public function execute()
    {
        if ($uniqueId = $this->getRequest()->getParam('unique_id')) {
            $session = $this->getSession();
            $result = $this->shareCartApi->restore($uniqueId, $this->fromAdmin());
            if (isset($result['success'])) {
                $redirectToCheckout = $this->shareCartHelper->getGeneralConfig('checkout_redirect');
                if (!$this->fromAdmin() && !$redirectToCheckout) {
                    $session->addSuccessMessage($result['success']);
                }
                $redirectPath = 'checkout/cart';
                if ($redirectToCheckout) {
                    $redirectPathConfig = $this->shareCartHelper->getGeneralConfig('checkout_path');
                    if ($redirectPathConfig) {
                        $redirectPath = $redirectPathConfig;
                    }
                }
                return $this->_redirect($redirectPath);
            } elseif (isset($result['error'])) {
                $session->addErrorMessage($result['error']);
            }
        }
        return $this->_redirect('/');
    }

    protected function fromAdmin()
    {
        return ($this->getRequest()->getParam('source') == "admin");
    }
}
