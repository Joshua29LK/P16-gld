<?php
declare(strict_types=1);

namespace Geissweb\Euvat\Setup\Patch\Data;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

/**
 * This patch is the replacement for the old InstallData.php script.
 * It does not implement the PatchVersionInterface because it is thought as an install, not upgrade script.
 */
class VatTraderAttributesPatch implements DataPatchInterface, PatchRevertableInterface
{
    const ADDRESS_ENTITY = 'customer_address';
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     * @param \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        AttributeRepositoryInterface $attributeRepository
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeRepository = $attributeRepository;
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
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Do Upgrade
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $this->addVatTraderAttributes();
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Adds customer address attributes for the data the service interface returns
     *
     * @return void
     */
    private function addVatTraderAttributes()
    {
        $attributes = [
            'vat_trader_name' => [
                'label' => 'VAT number company name',
                'type' => 'static',
                'input' => 'text',
                'required' => false,
                'position' => 150,
                'visible' => true,
                'system' => 0,
                'user_defined' => true,
                'is_user_defined' => 1,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
            ],
            'vat_trader_address' => [
                'label' => 'VAT number company address',
                'type' => 'static',
                'input' => 'textarea',
                'required' => false,
                'position' => 160,
                'visible' => true,
                'system' => 0,
                'user_defined' => true,
                'is_user_defined' => 1,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
            ],
        ];

        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        foreach ($attributes as $attributeCode => $attributeParams) {
            try {
                $this->attributeRepository->get(self::ADDRESS_ENTITY, $attributeCode);
            } catch (NoSuchEntityException $e) {
                $customerSetup->addAttribute(self::ADDRESS_ENTITY, $attributeCode, $attributeParams);
                $attribute = $customerSetup->getEavConfig()
                    ->clear()
                    ->getAttribute(self::ADDRESS_ENTITY, $attributeCode);
                $attribute->setData('used_in_forms', ['adminhtml_customer_address']);
                $attribute->save();
            }
        }
    }

    /**
     * Rollback all changes, done by this patch
     *
     * @return void
     */
    public function revert()
    {
        $this->moduleDataSetup->startSetup();
        try {
            $vatTraderNameAttributeId = $this->attributeRepository->get(
                self::ADDRESS_ENTITY,
                'vat_trader_name'
            )->getAttributeId();
            $vatTraderAddressAttributeId = $this->attributeRepository->get(
                self::ADDRESS_ENTITY,
                'vat_trader_address'
            )->getAttributeId();
            $gotAttributes = true;
        } catch (NoSuchEntityException $e) {
            $gotAttributes = false;
        }

        /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerSetup->removeAttribute(self::ADDRESS_ENTITY, 'vat_trader_name');
        $customerSetup->removeAttribute(self::ADDRESS_ENTITY, 'vat_trader_address');

        if ($gotAttributes) {
            $this->moduleDataSetup->getConnection()->delete(
                $this->moduleDataSetup->getTable('customer_eav_attribute'),
                ['attribute_id = ?' => $vatTraderNameAttributeId]
            );
            $this->moduleDataSetup->getConnection()->delete(
                $this->moduleDataSetup->getTable('customer_eav_attribute'),
                ['attribute_id = ?' => $vatTraderAddressAttributeId]
            );
        }

        $this->moduleDataSetup->endSetup();
    }
}
