<?php

namespace Balticode\CategoryConfigurator\Setup;

use Balticode\CategoryConfigurator\Model\ResourceModel\Step as StepResource;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\Product;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.3') < 0) {
            $this->addSortOrderInStepAttribute($setup);
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws LocalizedException
     */
    protected function addSortOrderInStepAttribute(ModuleDataSetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if ($eavSetup->getAttribute(Product::ENTITY, StepResource::ATTRIBUTE_CODE_SORT_ORDER_IN_STEP)) {
            return;
        }

        $eavSetup->addAttribute(Product::ENTITY, StepResource::ATTRIBUTE_CODE_SORT_ORDER_IN_STEP,
            [
                'type' => 'int',
                'label' => 'Sort Order In Step',
                'input' => 'text',
                'required' => false,
                'default' => '0',
                'global'   => ScopedAttributeInterface::SCOPE_STORE,
                'group' => '',
                'visible' => true,
                'user_defined' => true,
                'system' => 0,
                'attribute_set' => InstallData::ATTRIBUTE_SET_NAME_CONFIGURATOR_PRODUCTS
            ]
        );

        $configuratorProductsAttributeSetId = $this->getConfiguratorProductsAttributeSetId($setup);
        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);

        $eavSetup->addAttributeToSet(
            $entityTypeId,
            $configuratorProductsAttributeSetId,
            'General',
            StepResource::ATTRIBUTE_CODE_SORT_ORDER_IN_STEP
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return int
     * @throws LocalizedException
     */
    protected function getConfiguratorProductsAttributeSetId(ModuleDataSetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);

        return $eavSetup->getAttributeSetId($entityTypeId, InstallData::ATTRIBUTE_SET_NAME_CONFIGURATOR_PRODUCTS);
    }
}