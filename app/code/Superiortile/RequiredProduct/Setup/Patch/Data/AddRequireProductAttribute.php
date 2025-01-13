<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Superiortile\RequiredProduct\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Zend_Validate_Exception;

/**
 * Class Superiortile\RequiredProduct\Setup\Patch\Data\AddRequireProductAttribute
 */
class AddRequireProductAttribute implements DataPatchInterface, PatchRevertableInterface
{
    protected const NUMBER_ATTRIBUTES = 8;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Apply
     *
     * @return void
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        for ($i = 1; $i <= self::NUMBER_ATTRIBUTES; $i++) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                sprintf('require_name_%s', $i),
                [
                    'type' => 'varchar',
                    'label' => sprintf('Require Collection Name %s', $i),
                    'input' => 'text',
                    'source' => '',
                    'frontend' => '',
                    'required' => false,
                    'backend' => '',
                    'sort_order' => 100 * $i,
                    'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                    'default' => null,
                    'visible' => true,
                    'user_defined' => true,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'group' => 'Require Collection Attributes',
                    'used_in_product_listing' => true,
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => true
                ]
            );
            $eavSetup->addAttribute(
                Product::ENTITY,
                sprintf('require_tooltip_%s', $i),
                [
                    'type' => 'text',
                    'label' => sprintf('Require Collection Tooltip %s', $i),
                    'input' => 'textarea',
                    'source' => '',
                    'frontend' => '',
                    'required' => false,
                    'backend' => '',
                    'sort_order' => 100 * $i + 1,
                    'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                    'default' => null,
                    'visible' => true,
                    'user_defined' => true,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'group' => 'Require Collection Attributes',
                    'used_in_product_listing' => true,
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false
                ]
            );
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Revert
     *
     * @return void
     */
    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->removeAttribute(Product::ENTITY, 'require');

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Get Aliases
     *
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Get Dependencies
     *
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [

        ];
    }
}
