<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Superiortile\RequiredProduct\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Superiortile\RequiredProduct\Model\Product\Link;

/**
 * Class Superiortile\RequiredProduct\Setup\Patch\Data\CreateRequiredProductLink
 */
class CreateRequiredProductLink implements DataPatchInterface
{
    protected const NUMBER_ATTRIBUTES = 8;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Apply
     *
     * @return void
     */
    public function apply()
    {
        $setup = $this->moduleDataSetup;
        $productLinkAttribute = [];
        for ($i = 1; $i <= self::NUMBER_ATTRIBUTES; $i++) {
            $productLinkTypes = [
                'link_type_id' => 10 + $i,
                'code' => sprintf('requiredproducts%s', $i)
            ];

            $productLinkAttribute[] = [
                'link_type_id' => 10 + $i,
                'product_link_attribute_code' => 'position',
                'data_type' => 'int',
            ];
            $setup->getConnection()
                ->insertForce($setup->getTable('catalog_product_link_type'), $productLinkTypes);
        }

        $setup->getConnection()
            ->insertMultiple($setup->getTable('catalog_product_link_attribute'), $productLinkAttribute);
    }

    /**
     * Get Dependencies
     *
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [];
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
}
