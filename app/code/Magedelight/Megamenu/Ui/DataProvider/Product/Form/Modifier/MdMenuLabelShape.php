<?php
namespace Magedelight\Megamenu\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;

class MdMenuLabelShape extends AbstractModifier
{
    protected $arrayManager;

    public function __construct(ArrayManager $arrayManager)
    {
        $this->arrayManager = $arrayManager;
    }

    public function modifyMeta(array $meta)
    {
        $meta = $this->customizeColorPickerAttribute($meta);
        return $meta;
    }

    protected function customizeColorPickerAttribute(array $meta)
    {
        $attributeCode = 'md_menu_label_shape';
        $path = $this->arrayManager->findPath($attributeCode, $meta, null, 'children');
        
        if ($path) {
            $meta = $this->arrayManager->merge(
                $path,
                $meta,
                [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                                'dataType' => 'string',
                                'label' => __('Menu Label Shape'),
                                'dataScope' => $attributeCode,
                                'formElement' => 'radioset',
                                'additionalClasses' => 'md-label-shape'
                            ],
                        ],
                    ],
                ]
            );
        }
        return $meta;
    }

    public function modifyData(array $data)
    {
        return $data;
    }
}
