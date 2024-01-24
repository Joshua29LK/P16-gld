<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Shipping extends Column
{
    /**
     * @var \Amasty\Oaction\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Amasty\Oaction\Model\Source\Carriers
     */
    private $carrier;

    public function __construct(
        ContextInterface $context,
        \Amasty\Oaction\Helper\Data $helper,
        UiComponentFactory $uiComponentFactory,
        \Amasty\Oaction\Model\Source\Carriers $carrier,
        array $components = [],
        array $data = []
    ) {
        $this->carrier = $carrier;
        $this->_helper = $helper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepare()
    {
        parent::prepare();

        $data = $this->getData('config');
        $data['carrier'] = $this->carrier->toOptionArray();
        $data['default_carrier'] = $this->_helper->getModuleConfig('ship/carrier');
        $data['default_title'] = $this->_helper->getModuleConfig('ship/title');
        $data['show_title'] = $this->_helper->getModuleConfig('ship/comment') == "0"? 0: 1;

        $this->setData('config', $data);
    }
}
