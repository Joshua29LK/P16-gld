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
 * @package    Bss_Faqs
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\Faqs\Block\Adminhtml\Grid;

class CategoryGrid extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var Assign\AssignCategory
     */
    private $assignCategory;

    /**
     * Class constructor
     * @param Assign\AssignCategory $assignCategory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        Assign\AssignCategory $assignCategory,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->assignCategory = $assignCategory;
        parent::__construct($context, $data);
    }

    /**
     * Get element html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = $element->getElementHtml();
        $this->assignCategory->setFieldId($this->getFieldId());
        $this->assignCategory->setSourceId($this->getSourceId());
        $this->assignCategory->setIndex($this->getFaqId());
        $html .= $this->assignCategory->toHtml();
        return $html;
    }
}
