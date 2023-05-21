<?php
namespace Bss\CustomToolTipCO\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\Component\Modal;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Textarea;
use Magento\Ui\Component\Form\Element\DataType\Text;

class CustomOptions extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions
{
    const TOOL_TIP = 'tooltip_content';

    protected function getCommonContainerConfig($sortOrder)
    {
        $commonContainer = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Container::NAME,
                        'formElement' => Container::NAME,
                        'component' => 'Magento_Ui/js/form/components/group',
                        'breakLine' => false,
                        'showLabel' => false,
                        'additionalClasses' => 'admin__field-group-columns admin__control-group-equal',
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
            'children' => [
                static::FIELD_OPTION_ID => $this->getOptionIdFieldConfig(10),
                static::FIELD_TITLE_NAME => $this->getTitleFieldConfig(
                    20,
                    [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'label' => __('Option Title'),
                                    'component' => 'Magento_Catalog/component/static-type-input',
                                    'valueUpdate' => 'input',
                                    'imports' => [
                                        'optionId' => '${ $.provider }:${ $.parentScope }.option_id',
                                        'isUseDefault' => '${ $.provider }:${ $.parentScope }.is_use_default'
                                    ]
                                ],
                            ],
                        ],
                    ]
                ),
                static::FIELD_TYPE_NAME => $this->getTypeFieldConfig(30),
                static::FIELD_IS_REQUIRE_NAME => $this->getIsRequireFieldConfig(40),
                static::TOOL_TIP => $this->getToolTipFieldConfig(50)
            ]
        ];

        if ($this->locator->getProduct()->getStoreId()) {
            $useDefaultConfig = [
                'service' => [
                    'template' => 'Magento_Catalog/form/element/helper/custom-option-service',
                ]
            ];
            $titlePath = $this->arrayManager->findPath(static::FIELD_TITLE_NAME, $commonContainer, null)
                . static::META_CONFIG_PATH;
            $commonContainer = $this->arrayManager->merge($titlePath, $commonContainer, $useDefaultConfig);
        }

        return $commonContainer;
    }

    protected function getToolTipFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Tooltip content'),
                        'formElement' => Textarea::NAME,
                        'componentType' => Field::NAME,
                        'dataScope' => static::TOOL_TIP,
                        'sortOrder' => $sortOrder                    
                    ],
                ],
            ],
        ];
    }
}
