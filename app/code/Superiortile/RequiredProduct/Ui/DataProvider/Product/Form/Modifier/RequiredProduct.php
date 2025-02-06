<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Superiortile\RequiredProduct\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Related;
use Magento\Framework\Phrase;
use Magento\Ui\Component\Form\Fieldset;

/**
 * Class Superiortile\RequiredProduct\Ui\DataProvider\Product\Form\Modifier\RequiredProduct
 */
class RequiredProduct extends Related
{
    public const DATA_SCOPE_REQUIRED_PRODUCT = 'requiredproduct';
    public const DATA_SCOPE_REQUIRED_PRODUCT_1 = 'requiredproduct1';
    public const DATA_SCOPE_REQUIRED_PRODUCT_2 = 'requiredproduct2';
    public const DATA_SCOPE_REQUIRED_PRODUCT_3 = 'requiredproduct3';
    public const DATA_SCOPE_REQUIRED_PRODUCT_4 = 'requiredproduct4';
    public const DATA_SCOPE_REQUIRED_PRODUCT_5 = 'requiredproduct5';
    public const DATA_SCOPE_REQUIRED_PRODUCT_6 = 'requiredproduct6';
    public const DATA_SCOPE_REQUIRED_PRODUCT_7 = 'requiredproduct7';
    public const DATA_SCOPE_REQUIRED_PRODUCT_8 = 'requiredproduct8';
    public const GROUP_REQUIRED_PRODUCT = 'requiredproduct';

    /**
     * Get Data Scopes
     *
     * @return string[]
     */
    protected function getDataScopes()
    {
        return [
            static::DATA_SCOPE_REQUIRED_PRODUCT_1,
            static::DATA_SCOPE_REQUIRED_PRODUCT_2,
            static::DATA_SCOPE_REQUIRED_PRODUCT_3,
            static::DATA_SCOPE_REQUIRED_PRODUCT_4,
            static::DATA_SCOPE_REQUIRED_PRODUCT_5,
            static::DATA_SCOPE_REQUIRED_PRODUCT_6,
            static::DATA_SCOPE_REQUIRED_PRODUCT_7,
            static::DATA_SCOPE_REQUIRED_PRODUCT_8,
        ];
    }

    /**
     * Modify Meta
     *
     * @param  array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        return array_merge_recursive(
            $meta,
            [
                static::GROUP_REQUIRED_PRODUCT => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Required Product Collection'),
                                'componentType' => Fieldset::NAME,
                                'sortOrder' => 110,
                                'dataScope' => '',
                                'provider' => static::FORM_NAME . '.product_form_data_source',
                                'ns' => static::FORM_NAME,
                                'collapsible' => true,
                            ],
                        ],
                    ],
                    'children' => [
                        $this->scopePrefix . static::DATA_SCOPE_REQUIRED_PRODUCT_1 => $this->getRequiredProductFieldset(
                            static::DATA_SCOPE_REQUIRED_PRODUCT_1,
                            1
                        ),
                        $this->scopePrefix . static::DATA_SCOPE_REQUIRED_PRODUCT_2 => $this->getRequiredProductFieldset(
                            static::DATA_SCOPE_REQUIRED_PRODUCT_2,
                            2
                        ),
                        $this->scopePrefix . static::DATA_SCOPE_REQUIRED_PRODUCT_3 => $this->getRequiredProductFieldset(
                            static::DATA_SCOPE_REQUIRED_PRODUCT_3,
                            3
                        ),
                        $this->scopePrefix . static::DATA_SCOPE_REQUIRED_PRODUCT_4 => $this->getRequiredProductFieldset(
                            static::DATA_SCOPE_REQUIRED_PRODUCT_4,
                            4
                        ),
                        $this->scopePrefix . static::DATA_SCOPE_REQUIRED_PRODUCT_5 => $this->getRequiredProductFieldset(
                            static::DATA_SCOPE_REQUIRED_PRODUCT_5,
                            5
                        ),
                        $this->scopePrefix . static::DATA_SCOPE_REQUIRED_PRODUCT_6 => $this->getRequiredProductFieldset(
                            static::DATA_SCOPE_REQUIRED_PRODUCT_6,
                            6
                        ),
                        $this->scopePrefix . static::DATA_SCOPE_REQUIRED_PRODUCT_7 => $this->getRequiredProductFieldset(
                            static::DATA_SCOPE_REQUIRED_PRODUCT_7,
                            7
                        ),
                        $this->scopePrefix . static::DATA_SCOPE_REQUIRED_PRODUCT_8 => $this->getRequiredProductFieldset(
                            static::DATA_SCOPE_REQUIRED_PRODUCT_8,
                            8
                        ),
                    ],
                ],
            ]
        );
    }

    /**
     * Get Required Product Fieldset
     *
     * @param string $scope
     * @param int $number
     * @return array
     */
    protected function getRequiredProductFieldset($scope, $number = 1)
    {
        $content = __(
            'Required products.'
        );
        return [
            'children' => [
                'button_set' => $this->getButtonSet(
                    $content,
                    __('Add Required Products'),
                    $this->scopePrefix . $scope
                ),
                'modal' => $this->getGenericModal(
                    __('Add Required Products'),
                    $this->scopePrefix . $scope
                ),
                $scope => $this->getGrid($this->scopePrefix . $scope),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Required Collection %1', $number),
                        'collapsible' => false,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'sortOrder' => $number * 10,
                    ],
                ],
            ]
        ];
    }

    /**
     * Retrieve button set
     *
     * @param Phrase $content
     * @param Phrase $buttonTitle
     * @param string $scope
     * @return array
     * @since 101.0.0
     */
    protected function getButtonSet(Phrase $content, Phrase $buttonTitle, $scope)
    {
        $modalTarget = $this->scopeName . '.' . static::GROUP_REQUIRED_PRODUCT . '.' . $scope . '.modal';
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'content' => $content,
                        'template' => 'ui/form/components/complex',
                    ],
                ],
            ],
            'children' => [
                'button_' . $scope => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' => $modalTarget,
                                        'actionName' => 'toggleModal',
                                    ],
                                    [
                                        'targetName' => $modalTarget . '.' . $scope . '_product_listing',
                                        'actionName' => 'render',
                                    ]
                                ],
                                'title' => $buttonTitle,
                                'provider' => null,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
