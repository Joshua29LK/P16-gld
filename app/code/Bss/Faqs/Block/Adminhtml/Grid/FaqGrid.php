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

class FaqGrid extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var Assign\AssignFaq
     */
    private $assignFaq;

    /**
     * Class constructor
     * @param Assign\AssignFaq $assignFaq
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        Assign\AssignFaq $assignFaq,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->assignFaq = $assignFaq;
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
        $this->assignFaq->setType($this->getType());
        $this->assignFaq->setFieldId($this->getFieldId());
        $this->assignFaq->setSourceId($this->getSourceId());
        if ($this->getType() == 'category') {
            $this->assignFaq->setIndex($this->getFaqCategoryId());
        } elseif ($this->getType() == 'related') {
            $this->assignFaq->setIndex($this->getFaqId());
        }
        $html .= $this->assignFaq->toHtml();
        return $html;
    }
}
