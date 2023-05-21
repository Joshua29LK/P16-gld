<?php
namespace FME\CanonicalUrl\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    protected $_helper;
    protected $categorySetupFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \FME\CanonicalUrl\Helper\Data $helper,
        CategorySetupFactory $categorySetupFactory
    ){
        $this->_helper = $helper;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);
        $catalogSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'override_canonical_url',
            [
                'group' => 'Search Engine Optimization',
                'label' => 'Override Canonical Url',
                'input' => 'text',
                'class' => 'override_canonical_url',
                'type' => 'varchar',
                'global' =>  \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'apply_to' => '',
                'visible_on_front' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'sortOrder' => 1,
            ]
        );

        $catalogSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'canonical_primary_category_url',
            [   
                'group' => 'Search Engine Optimization',
                'type' => 'varchar',
                'frontend' => '',
                'backend' => '',
                'label' => 'Canonical Primary Category Url',
                'input' => 'select',
                'class' => '',
                'source' => 'FME\CanonicalUrl\Model\Config\Source\ListCategories',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => 'config',
                'visible_on_front' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'sortOrder' => 2,
            ]
        );

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'override_canonical_url',
            [
                'group' => 'Search Engine Optimization',
                'label' => 'Override Canonical Url',
                'input' => 'text',
                'type' => 'varchar',
                'global' =>  \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'required' => false,
                'visible' => true,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'sortOrder' => 1,
            ]
        );

        $setup->endSetup();

        
    }
}
