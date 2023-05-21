<?php
namespace Bss\CustomBalticodeCategoryConfigurator\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * InstallData constructor.
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
    
        $setup->startSetup();

        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'glass_shape_height',
            [
                'type' => 'varchar',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'default' => '',
                'label' => 'Glass Shape Height',
                'note' => 'Specify the height of a glass shape.'
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'glass_shape_width',
            [
                'type' => 'varchar',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'default' => '',
                'label' => 'Glass Shape Width',
                'note' => 'Specify the width of a glass shape.'
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'min_glass_shape_height',
            [
                'type' => 'varchar',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'default' => '',
                'label' => 'Minimum Glass Shape Height',
                'note' => 'Specify the minimum height of a glass shape.'
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'min_glass_shape_width',
            [
                'type' => 'varchar',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'default' => '',
                'label' => 'Minimum Glass Shape Width',
                'note' => 'Specify the minimum width of a glass shape.'
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'max_glass_shape_height',
            [
                'type' => 'varchar',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'default' => '',
                'label' => 'Maximum Glass Shape Height',
                'note' => 'Specify the maximum height of a glass shape.'
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'max_glass_shape_width',
            [
                'type' => 'varchar',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'default' => '',
                'label' => 'Maximum Glass Shape Width',
                'note' => 'Specify the maximum width of a glass shape.'
            ]
        );

        $setup->endSetup();
    }
}
