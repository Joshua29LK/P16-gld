<?php

namespace Woom\CmsTree\Ui\Component\Form\Fieldset;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Framework\App\RequestInterface;

class Websites extends Fieldset
{
    /**
     * Store manager
     *
     * @var StoreManager
     */
    private $storeManager;

    /**
     * Request interface
     *
     * @var RequestInterface
     */
    private $request;

    /**
     * Websites constructor.
     *
     * @param ContextInterface $context
     * @param StoreManager     $storeManager
     * @param RequestInterface $request
     * @param array            $components
     * @param array            $data
     */
    public function __construct(
        ContextInterface $context,
        StoreManager $storeManager,
        RequestInterface $request,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->storeManager = $storeManager;
        $this->request = $request;
    }

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        if ($this->storeManager->isSingleStoreMode()
            || $this->storeManager->hasSingleStore()
        ) {
            $this->_data['config']['componentDisabled'] = true;
        }
    }
}
