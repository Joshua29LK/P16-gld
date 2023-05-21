<?php
/**
 * @author CynoInfotech Team
 * @package Cynoinfotech_IndexFollow
 */
namespace Cynoinfotech\IndexFollow\Model\Config\Source; 
  
use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\DB\Ddl\Table;
  
class FollowOptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
 
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = array(           
            array('label'=>'Follow', 'value'=>'1'),
            array('label'=>'No Follow', 'value'=>'2'),
        );
        return $this->_options;
    }
  
    /**
     * Get a text for option value
     * @param string|integer $value
     * @return string|bool
     */
    public function getOptionText($value)
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }

        return false;
    }
}