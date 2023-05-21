<?php
/**
 * @author CynoInfotech Team
 * @package Cynoinfotech_IndexFollow
 */
namespace Cynoinfotech\IndexFollow\Block\Adminhtml\Form\Field;

class IndexValue extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * Construct
     * @param \Magento\Framework\View\Element\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Store\Model\StoreManager $storeManager,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->storeManager = $storeManager;
    }
    
    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
     
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $types = array('1'=>' Index ','2'=>'No Index');
            
            foreach ($types as $key => $value) {
                 $this->addOption($key, $value);
            }
        }

        return parent::_toHtml();
    }
}
