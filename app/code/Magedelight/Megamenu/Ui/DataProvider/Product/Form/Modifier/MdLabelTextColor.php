<?php
namespace Magedelight\Megamenu\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;

class MdLabelTextColor extends AbstractModifier
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
        $attributeCode = 'md_label_text_color';
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
                                'component' => 'Magento_Ui/js/form/element/color-picker',
                                'dataType' => 'text',
                                'elementTmpl' => 'ui/form/element/color-picker',
                                'label' => __('Text Color (hex)'),
                                'dataScope' => $attributeCode,
                                'formElement' => 'colorPicker',
                                'colorFormat' => 'hax',
                                'colorPickerMode' => 'full'
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
