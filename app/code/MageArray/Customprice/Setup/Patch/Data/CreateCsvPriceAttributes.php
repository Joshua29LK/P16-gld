<?php

namespace MageArray\Customprice\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use MageArray\Customprice\Model\Attribute\Source\Globaloption;
use MageArray\Customprice\Model\Attribute\Source\ApplyCsvType;

class CreateCsvPriceAttributes implements DataPatchInterface
{
    /**
     * [$moduleDataSetup description]
     *
     * @var [type]
     */
    private $moduleDataSetup;

    /**
     * [$eavSetupFactory description]
     *
     * @var [type]
     */
    private $eavSetupFactory;

    /**
     * [__construct description]
     *
     * @param ModuleDataSetupInterface $moduleDataSetup [description]
     * @param EavSetupFactory          $eavSetupFactory [description]
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $isRowLabels = $eavSetup->getAttribute(Product::ENTITY, 'row_labels', 'attribute_id');
        if (!is_numeric($isRowLabels)) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'row_labels',
                [
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Row Labels',
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => null,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'system' => 1,
                    'group' => 'CSV Pricing (Override Global Settings)',
                    'sort_order' => 2,
                ]
            );
        }

        $isColumnLabels = $eavSetup->getAttribute(
            Product::ENTITY,
            'column_labels',
            'attribute_id'
        );
        if (!is_numeric($isColumnLabels)) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'column_labels',
                [
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Column Labels',
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => null,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'system' => 1,
                    'group' => 'CSV Pricing (Override Global Settings)',
                    'sort_order' => 3,
                ]
            );
        }

        $isPricemin = $eavSetup->getAttribute(Product::ENTITY, 'pricemin', 'attribute_id');
        if (!is_numeric($isPricemin)) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'pricemin',
                [
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Price Min',
                    'input' => 'select',
                    'class' => '',
                    'source' => Globaloption::class,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => null,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'system' => 1,
                    'group' => 'CSV Pricing (Override Global Settings)',
                    'sort_order' => 5,
                ]
            );
        }

        $isIncludeBaseprice = $eavSetup->getAttribute(
            Product::ENTITY,
            'include_baseprice',
            'attribute_id'
        );

        if (!is_numeric($isIncludeBaseprice)) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'include_baseprice',
                [
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Include Base Price',
                    'input' => 'select',
                    'class' => '',
                    'source' => Globaloption::class,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => null,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'system' => 1,
                    'group' => 'CSV Pricing (Override Global Settings)',
                    'sort_order' => 6,
                ]
            );
        }

        $isDisplayAsDropdown = $eavSetup->getAttribute(
            Product::ENTITY,
            'display_as_dropdown',
            'attribute_id'
        );
        if (!is_numeric($isDisplayAsDropdown)) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'display_as_dropdown',
                [
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Display as a Dropdown',
                    'input' => 'select',
                    'class' => '',
                    'source' => Globaloption::class,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => null,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'system' => 1,
                    'group' => 'CSV Pricing (Override Global Settings)',
                    'sort_order' => 7,
                ]
            );
        }

        $isCsvDelimiter = $eavSetup->getAttribute(
            Product::ENTITY,
            'csv_delimiter',
            'attribute_id'
        );
        if (!is_numeric($isCsvDelimiter)) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'csv_delimiter',
                [
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'CSV delimiter',
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => null,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'system' => 1,
                    'group' => 'CSV Pricing (Override Global Settings)',
                    'sort_order' => 9,
                ]
            );
        }

        $isMaxError = $eavSetup->getAttribute(Product::ENTITY, 'max_error', 'attribute_id');
        if (!is_numeric($isMaxError)) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'max_error',
                [
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Max Error',
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => null,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'system' => 1,
                    'group' => 'CSV Pricing (Override Global Settings)',
                    'sort_order' => 10,
                ]
            );
        }

        $isMinError = $eavSetup->getAttribute(Product::ENTITY, 'min_error', 'attribute_id');
        if (!is_numeric($isMinError)) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'min_error',
                [
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Min Error',
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => null,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'system' => 1,
                    'group' => 'CSV Pricing (Override Global Settings)',
                    'sort_order' => 11,
                ]
            );
        }

        $isMaxMinDefaultDisplay = $eavSetup->getAttribute(
            Product::ENTITY,
            'max_min_default_display',
            'attribute_id'
        );
        if (!is_numeric($isMaxMinDefaultDisplay)) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'max_min_default_display',
                [
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Display Max-Min Value By Default',
                    'input' => 'select',
                    'class' => '',
                    'source' => Globaloption::class,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => null,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'system' => 1,
                    'group' => 'CSV Pricing (Override Global Settings)',
                    'sort_order' => 12,
                ]
            );
        }

        $isNotFoundSizeMsg = $eavSetup->getAttribute(
            Product::ENTITY,
            'not_found_size_msg',
            'attribute_id'
        );
        if (!is_numeric($isNotFoundSizeMsg)) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'not_found_size_msg',
                [
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Not Found Size Message',
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => null,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'system' => 1,
                    'group' => 'CSV Pricing (Override Global Settings)',
                    'sort_order' => 13,
                ]
            );
        }

        $isCsvPriceMarkupType = $eavSetup->getAttribute(
            Product::ENTITY,
            'csv_price_markup_type',
            'attribute_id'
        );
        if (!is_numeric($isCsvPriceMarkupType)) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'csv_price_markup_type',
                [
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Markup Type',
                    'input' => 'select',
                    'class' => '',
                    'source' => \MageArray\Customprice\Model\Attribute\Source\Markuptype::class,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => null,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'system' => 1,
                    'group' => 'CSV Pricing (Override Global Settings)',
                    'sort_order' => 14,
                ]
            );
        }

        $isCsvPriceMarkupValue = $eavSetup->getAttribute(
            Product::ENTITY,
            'csv_price_markup_value',
            'attribute_id'
        );
        if (!is_numeric($isCsvPriceMarkupValue)) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'csv_price_markup_value',
                [
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Markup Value',
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => null,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'system' => 1,
                    'group' => 'CSV Pricing (Override Global Settings)',
                    'sort_order' => 15,
                ]
            );
        }

        $applyCsvType = $eavSetup->getAttribute(
            Product::ENTITY,
            'apply_csv_type',
            'attribute_id'
        );
        if (!is_numeric($applyCsvType)) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'apply_csv_type',
                [
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Apply CSV Type',
                    'input' => 'select',
                    'class' => '',
                    'source' => ApplyCsvType::class,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => null,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'system' => 1,
                    'group' => 'CSV Pricing (Override Global Settings)',
                    'sort_order' => 0,
                ]
            );
        }

        $isCsvCsvLabel = $eavSetup->getAttribute(
            Product::ENTITY,
            'csv_csv_label',
            'attribute_id'
        );
        if (!is_numeric($isCsvCsvLabel)) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'csv_csv_label',
                [
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Option Wise CSV Label',
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => null,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'system' => 1,
                    'group' => 'CSV Pricing (Override Global Settings)',
                    'sort_order' => 16,
                ]
            );
        }

        $isCsvPriceUnit = $eavSetup->getAttribute(
            Product::ENTITY,
            'csv_price_unit',
            'attribute_id'
        );
        if (!is_numeric($isCsvPriceUnit)) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'csv_price_unit',
                [
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Unit',
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => null,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'system' => 1,
                    'group' => 'CSV Pricing (Override Global Settings)',
                    'sort_order' => 4,
                ]
            );
        }

        $isCsvDisplayAllDropdown = $eavSetup->getAttribute(
            Product::ENTITY,
            'csv_display_all_dropdown',
            'attribute_id'
        );
        if (!is_numeric($isCsvDisplayAllDropdown)) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'csv_display_all_dropdown',
                [
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Display all range value',
                    'input' => 'select',
                    'class' => '',
                    'source' => Globaloption::class,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => null,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'system' => 1,
                    'group' => 'CSV Pricing (Override Global Settings)',
                    'sort_order' => 8,
                ]
            );
        }
    }
}
