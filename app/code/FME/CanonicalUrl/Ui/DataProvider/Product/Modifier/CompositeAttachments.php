<?php
namespace FME\CanonicalUrl\Ui\DataProvider\Product\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\DataType\Date;
use Magento\Ui\Component\Form\Element\Radio;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\Textarea;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\DataType\Boolean;

class CompositeAttachments extends AbstractModifier
{
 
    private $locator;
    protected $_registry;
    protected $_helper;
    protected $_coreResource;
    protected $arrayManager;

    public function __construct(
        LocatorInterface $locator,
        \FME\CanonicalUrl\Helper\Data $helper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\ResourceConnection $coreResource,
        ArrayManager $arrayManager
    ) {
        $this->locator = $locator;
        $this->_coreResource = $coreResource;
        $this->_helper = $helper;
        $this->arrayManager = $arrayManager;
        $this->_registry = $registry;
    }

    public function modifyData(array $data)
    {
        if ($this->_helper->isEnabledInFrontend()) {
            $productId = $this->locator->getProduct()->getId();
            $modelId = $this->locator->getProduct()->getId();

            if (!empty($productId)) {
                 
                  
                if(empty($this->locator->getProduct()->getCanonicalPrimaryCategoryUrl())){
                    $data[$modelId][static::DATA_SOURCE_DEFAULT]['canonical_primary_category_url'] = 'config';
                 
                }

            }
        }
  
        return $data;
    }

    public function modifyMeta(array $meta)
    {
 
        return $meta;
    }
}