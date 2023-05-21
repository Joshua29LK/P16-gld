<?php

namespace Itoris\ProductPriceFormula\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Container;

class PriceFormula extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier {
    
    public function modifyMeta(array $meta) {
        $this->meta = $meta;
        $this->createPriceFormulaPanel();
        return $this->meta;
    }
    
    public function modifyData(array $data) {
        return $data;
    }
    
    protected function createPriceFormulaPanel() {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        if (!$this->getDataHelper()->isEnabled()) return;
        
        $block = $this->_objectManager->create('Itoris\ProductPriceFormula\Block\Adminhtml\Catalog\Product\Edit\Tab\Formulatab');
        
        $this->meta = array_replace_recursive(
            $this->meta,
            [
                'product_price_formula' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => $block->escapeHtml(__('Product Price Formula')),
                                'componentType' => Fieldset::NAME,
                                'dataScope' => 'data.product',
                                'collapsible' => true,
                                'sortOrder' => 20,
                            ],
                        ],
                    ],
                    'children' => [
                        'price-formula' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'label' => null,
                                        'formElement' => Container::NAME,
                                        'componentType' => Container::NAME,
                                        'template' => 'ui/form/components/complex',
                                        'sortOrder' => 10,
                                        'content' => $block->toHtml(),
                                    ],
                                ],
                            ],
                            'children' => [
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $this;
    }
    
    public function getDataHelper() {
        return $this->_objectManager->get('Itoris\ProductPriceFormula\Helper\Data');
    }
}