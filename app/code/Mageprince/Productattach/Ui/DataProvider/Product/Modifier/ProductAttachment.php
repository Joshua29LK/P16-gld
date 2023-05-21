<?php

/**
 * MagePrince
 * Copyright (C) 2020 Mageprince <info@mageprince.com>
 *
 * @package Mageprince_Productattach
 * @copyright Copyright (c) 2020 Mageprince (http://www.mageprince.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MagePrince <info@mageprince.com>
 */

namespace Mageprince\Productattach\Ui\DataProvider\Product\Modifier;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductLinkInterface;
use Mageprince\Productattach\Model\ResourceModel\Productattach\CollectionFactory as productAttachmentCollectionFactory;
use Magento\Catalog\Api\ProductLinkRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use Mageprince\Productattach\Helper\Data as dataHelper;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Modal;
use Mageprince\Productattach\Model\Config\Source\Status;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;

class ProductAttachment extends AbstractModifier
{
    /**
     * Data scope
     */
    const DATA_SCOPE = '';

    const DATA_SCOPE_PRODUCTATTACHMENT = 'productattach';

    const GROUP_PRODUCTATTACHMENT = 'productattach';

    /**
     * @var string
     */
    private static $previousGroup = 'search-engine-optimization';

    /**
     * @var int
     */
    private static $sortOrder = 150;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ProductLinkRepositoryInterface
     */
    protected $productLinkRepository;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Status
     */
    protected $status;

    /**
     * @var AttributeSetRepositoryInterface
     */
    protected $attributeSetRepository;

    /**
     * @var string
     */
    protected $scopeName;

    /**
     * @var productAttachmentCollectionFactory
     */
    protected $productAttachmentCollection;

    /**
     * @var \Mageprince\Productattach\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productattachRelationCollection;

    /**
     * @var string
     */
    protected $scopePrefix;

    /**
     * @var \Magento\Catalog\Ui\Component\Listing\Columns\Price
     */
    private $priceModifier;

    /**
     * ProductAttachment constructor.
     * @param LocatorInterface $locator
     * @param UrlInterface $urlBuilder
     * @param dataHelper $helper
     * @param ProductLinkRepositoryInterface $productLinkRepository
     * @param ProductRepositoryInterface $productRepository
     * @param Status $status
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Mageprince\Productattach\Model\ResourceModel\Product\CollectionFactory $productattachRelationCollection
     * @param productAttachmentCollectionFactory $productAttachmentCollection
     * @param AttributeSetRepositoryInterface $attributeSetRepository
     * @param string $scopeName
     * @param string $scopePrefix
     */
    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder,
        dataHelper $helper,
        ProductLinkRepositoryInterface $productLinkRepository,
        ProductRepositoryInterface $productRepository,
        Status $status,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mageprince\Productattach\Model\ResourceModel\Product\CollectionFactory $productattachRelationCollection,
        productAttachmentCollectionFactory $productAttachmentCollection,
        AttributeSetRepositoryInterface $attributeSetRepository,
        $scopeName = '',
        $scopePrefix = ''
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
        $this->helper = $helper;
        $this->_storeManager = $storeManager;
        $this->productLinkRepository = $productLinkRepository;
        $this->productattachRelationCollection = $productattachRelationCollection;
        $this->productAttachmentCollection = $productAttachmentCollection;
        $this->productRepository = $productRepository;
        $this->status = $status;
        $this->attributeSetRepository = $attributeSetRepository;
        $this->scopeName = $scopeName;
        $this->scopePrefix = $scopePrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                static::GROUP_PRODUCTATTACHMENT => [
                    'children' => [
                        $this->scopePrefix . static::DATA_SCOPE_PRODUCTATTACHMENT => $this->getProductAttachmentFieldset(),
                    ],
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Product Attachments'),
                                'collapsible' => true,
                                'componentType' => Fieldset::NAME,
                                'dataScope' => static::DATA_SCOPE,
                                'sortOrder' =>
                                    $this->getNextGroupSortOrder(
                                        $meta,
                                        self::$previousGroup,
                                        self::$sortOrder
                                    ),
                            ],
                        ],

                    ],
                ],
            ]
        );

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->locator->getProduct();
        $productId = $product->getId();
        if (!$productId) {
            return $data;
        }
        foreach ($this->getDataScopes() as $dataScope) {
            $data[$productId]['links'][$dataScope] = [];
            $productAttachmentRelationCollection = $this->productattachRelationCollection->create()
                ->addFieldToFilter('product_id', ['eq' => $productId]);
            foreach ($productAttachmentRelationCollection as $prodColl) {
                $data[$productId]['links'][$dataScope][] = $this->fillData($prodColl['productattach_id']);
            }

        }

        return $data;
    }

    /**
     * Prepare data column
     *
     * @param ProductInterface $linkedProduct
     * @param ProductLinkInterface $linkItem
     * @return array
     */
    protected function fillData($productattachmentIds)
    {
        $attachmentCollection = $this->productAttachmentCollection->create()
                    ->addFieldToFilter('productattach_id', ['eq' => $productattachmentIds])
                    ->getFirstItem();
        $attachmentCollection->getProductattachId();

        return [
            'id' => $attachmentCollection->getProductattachId(),
            'name' => $attachmentCollection->getName(),
            'file' => $attachmentCollection->getFile(),
            'file_ext' => $attachmentCollection->getFileExt(),
            'status' => $this->status->getOptionText($attachmentCollection->getActive())
        ];
    }

    /**
     * Retrieve all data scopes
     *
     * @return array
     */
    protected function getDataScopes()
    {
        return [
            static::DATA_SCOPE_PRODUCTATTACHMENT,
        ];
    }

    /**
     * Prepares config for the ProductAttachment products fieldset
     *
     * @return array
     */
    protected function getProductAttachmentFieldset()
    {
        $content = __(
            'Product attachments used to provide product related documents.'
        );

        return [
            'children' => [
                'button_set' => $this->getButtonSet(
                    $content,
                    __('Add Attachment'),
                    $this->scopePrefix . static::DATA_SCOPE_PRODUCTATTACHMENT
                ),
                'modal' => $this->getGenericModal(
                    __('Add Attachment'),
                    $this->scopePrefix . static::DATA_SCOPE_PRODUCTATTACHMENT
                ),
                static::DATA_SCOPE_PRODUCTATTACHMENT => $this->getGrid($this->scopePrefix . static::DATA_SCOPE_PRODUCTATTACHMENT),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Product Attachments'),
                        'collapsible' => false,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'sortOrder' => 10,
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
     */
    protected function getButtonSet(Phrase $content, Phrase $buttonTitle, $scope)
    {
        $modalTarget = $this->scopeName . '.' . static::GROUP_PRODUCTATTACHMENT . '.' . $scope . '.modal';

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
                                        'targetName' => $modalTarget . '.' . $scope . '_productattach_listing',
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

    /**
     * Prepares config for modal slide-out panel
     *
     * @param Phrase $title
     * @param string $scope
     * @return array
     */
    protected function getGenericModal(Phrase $title, $scope)
    {
        $listingTarget = $scope . '_productattach_listing';

        $modal = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'options' => [
                            'title' => $title,
                            'buttons' => [
                                [
                                    'text' => __('Cancel'),
                                    'actions' => [
                                        'closeModal'
                                    ]
                                ],
                                [
                                    'text' => __('Add Selected Attachments'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = ' . $listingTarget,
                                            'actionName' => 'save'
                                        ],
                                        'closeModal'
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [
                $listingTarget => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => false,
                                'componentType' => 'insertListing',
                                'dataScope' => $listingTarget,
                                'externalProvider' =>$listingTarget . '.' . $listingTarget . '_data_source',
                                'selectionsProvider' => $listingTarget . '.' . $listingTarget . '.' . $listingTarget .'_columns.ids',
                                'ns' => $listingTarget,
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => true,
                                'dataLinks' => [
                                    'imports' => false,
                                    'exports' => true
                                ],
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $modal;
    }

    /**
     * Retrieve grid
     *
     * @param string $scope
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getGrid($scope)
    {
        $dataProvider = $scope . '_productattach_listing';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__field-wide',
                        'componentType' => DynamicRows::NAME,
                        'label' => null,
                        'columnsHeader' => false,
                        'columnsHeaderAfterRender' => true,
                        'renderDefaultRecord' => false,
                        'template' => 'ui/dynamic-rows/templates/grid',
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
                        'addButton' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => 'data.links',
                        'deleteButtonLabel' => __('Remove'),
                        'dataProvider' => $dataProvider,
                        'map' => [
                            'id' => 'productattach_id',
                            'name' => 'name',
                            'file' => 'file',
                            'file_ext' => 'file_ext',
                            'status' => 'active'
                        ],
                        'links' => [
                            'insertData' => '${ $.provider }:${ $.dataProvider }',
                            '__disableTmpl' => ['insertData' => false],
                        ],
                        'sortOrder' => 2,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'container',
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                            ],
                        ],
                    ],
                    'children' => $this->fillMeta(),
                ],
            ],
        ];
    }

    /**
     * Retrieve meta column
     *
     * @return array
     */
    protected function fillMeta()
    {
        return [
            'id' => $this->getTextColumn('id', false, __('ID'), 0),
            'name' => $this->getTextColumn('name', false, __('Name'), 1),
            'file' => $this->getTextColumn('file', false, __('File'), 2),
            'file_ext' => $this->getTextColumn('file_ext', false, __('File Type'), 3),
            'status' => $this->getTextColumn('status', false, __('Status'), 4),
            'actionDelete' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'data-grid-actions-cell',
                            'componentType' => 'actionDelete',
                            'dataType' => Text::NAME,
                            'label' => __('Actions'),
                            'sortOrder' => 70,
                            'fit' => true,
                        ],
                    ],
                ],
            ],

        ];
    }

    /**
     * Retrieve text column structure
     *
     * @param string $dataScope
     * @param bool $fit
     * @param Phrase $label
     * @param int $sortOrder
     * @return array
     */
    protected function getTextColumn($dataScope, $fit, Phrase $label, $sortOrder)
    {
        $column = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                        'component' => 'Magento_Ui/js/form/element/text',
                        'dataType' => Text::NAME,
                        'dataScope' => $dataScope,
                        'fit' => $fit,
                        'label' => $label,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];

        return $column;
    }
}
