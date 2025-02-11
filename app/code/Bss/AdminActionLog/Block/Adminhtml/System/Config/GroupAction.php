<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_AdminActionLog
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\AdminActionLog\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;

class GroupAction extends \Magento\Config\Block\System\Config\Form\Field
{
    const CONFIG_PATH = 'action_log_bss/general/groupaction';

    /**
     * @var string
     */
    protected $_template = 'Bss_AdminActionLog::system/config/groupaction.phtml';

    /**
     * @var null
     */
    protected $values = null;

    /**
     * @var \Bss\AdminActionLog\Model\Config\Source\GroupAction
     */
    protected $groupaction;

    /**
     * GroupAction constructor.
     * @param Context $context
     * @param \Bss\AdminActionLog\Model\Config\Source\GroupAction $groupaction
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Bss\AdminActionLog\Model\Config\Source\GroupAction $groupaction,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->groupaction = $groupaction;
    }

    /**
     * Get Element Html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setNamePrefix($element->getName())->setHtmlId($element->getHtmlId());
        return $this->_toHtml();
    }

    /**
     * Get Values
     *
     * @return array
     */
    public function getValues()
    {
        $values = [];
        foreach ($this->groupaction->toOptionArray() as $value) {
            $values[$value['value']] = $value['label'];
        }
        return $values;
    }

    /**
     * Get Is Checked
     *
     * @param $name
     * @return bool
     */
    public function getIsChecked($name)
    {
        return in_array($name, $this->getCheckedValues());
    }

    /**
     * Get Checked Values
     *
     * @return array|null
     */
    public function getCheckedValues()
    {
        if (is_null($this->values)) {
            $data = $this->getConfigData();
            if (isset($data[self::CONFIG_PATH])) {
                $data = $data[self::CONFIG_PATH];
            } else {
                $data = '';
            }
            $this->values = explode(',', $data);
        }
        return $this->values;
    }
}
