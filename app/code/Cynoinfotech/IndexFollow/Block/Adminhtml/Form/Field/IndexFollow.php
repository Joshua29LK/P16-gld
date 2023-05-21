<?php
/**
 * @author CynoInfotech Team
 * @package Cynoinfotech_IndexFollow
 */
namespace Cynoinfotech\IndexFollow\Block\Adminhtml\Form\Field;

class IndexFollow extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var Countrygroup
     */
        
    protected $_followValueRenderer;
    
    protected $_indexValueRenderer;
    
    protected $_indexFollowEnableRenderer;
    
    /**
     * Retrieve group column renderer
     * @return Countrygroup
     */
   
    
    
    protected function _getFollowValueRenderer()
    {
        if (!$this->_followValueRenderer) {
            $this->_followValueRenderer = $this->getLayout()->createBlock(
                'Cynoinfotech\IndexFollow\Block\Adminhtml\Form\Field\FollowValue',
                '',
                array('data' => array('is_render_to_js_template' => true))
            );
        }

        return $this->_followValueRenderer;
    }
    
    protected function _getIndexValueRenderer()
    {
        if (!$this->_indexValueRenderer) {
            $this->_indexValueRenderer = $this->getLayout()->createBlock(
                'Cynoinfotech\IndexFollow\Block\Adminhtml\Form\Field\IndexValue',
                '',
                array('data' => array('is_render_to_js_template' => true))
            );
        }

        return $this->_indexValueRenderer;
    }
    
    protected function _getIndexFollowEnableRenderer()
    {
        if (!$this->_indexFollowEnableRenderer) {
            $this->_indexFollowEnableRenderer = $this->getLayout()->createBlock(
                'Cynoinfotech\IndexFollow\Block\Adminhtml\Form\Field\IndexFollowEnable',
                '',
                array('data' => array('is_render_to_js_template' => true))
            );
        }

        return $this->_indexFollowEnableRenderer;
    }
    

    /**
     * Prepare to render
     * @return void
     */
     
    protected function _prepareToRender()
    {
        
         $this->addColumn('url', array('label' => __('URL')));
        
         $this->addColumn(
             'follow_value',
             array('label' => __('Follow Value'), 'renderer' => $this->_getFollowValueRenderer())
         );
         
         $this->addColumn(
             'index_value',
             array('label' => __('Index Value'), 'renderer' => $this->_getIndexValueRenderer())
         );
         
          $this->addColumn(
              'indexfollow_enable',
              array('label' => __('Enable'), 'renderer' => $this->_getIndexFollowEnableRenderer())
          );
                    
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add New');
    }

    /**
     * Prepare existing row data object
     * @param \Magento\Framework\DataObject $row
     * @return void
     */
     
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = array();
    
        $optionExtraAttr['option_' . $this->_getFollowValueRenderer()
        ->calcOptionHash($row->getData('follow_value'))] = 'selected="selected"';
   
        $optionExtraAttr['option_' . $this->_getIndexValueRenderer()
        ->calcOptionHash($row->getData('index_value'))] = 'selected="selected"';
    
        $optionExtraAttr['option_' . $this->_getIndexFollowEnableRenderer()
        ->calcOptionHash($row->getData('indexfollow_enable'))] = 'selected="selected"';
        
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
                
        
    }
}
