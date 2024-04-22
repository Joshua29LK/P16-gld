<?php
namespace Magedelight\Megamenu\Model\Config\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class StoreButton extends Field
{
    /**
     * @var string
     */
    protected $_template = 'Magedelight_Megamenu::system/config/button/storebutton.phtml';

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Remove scope label
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(
            [
                'menu_active_url' => $this->getUrl('megamenu/cache/cleanmenu')
            ]
        );
        return $this->_toHtml();
    }

    /**
     * Return ajax url for send button
     *
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('megamenu/cache/cleanmenu');
    }

    /**
     * Generate send button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'store_button',
                'label' => __('Flush Store Menu'),
            ]
        );

        return $button->toHtml();
    }
}
