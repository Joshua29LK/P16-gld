<?php
declare(strict_types=1);

namespace Geissweb\Euvat\Setup\Patch\Data;

use Magento\Framework\DB\DataConverter\SerializedToJson;
use Magento\Framework\DB\FieldDataConversionException;
use Magento\Framework\DB\FieldDataConverterFactory;
use Magento\Framework\DB\Select\QueryModifierFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\Module\Manager;
use Psr\Log\LoggerInterface;

/**
 * This patch is the replacement for the old UpgradeData.php script
 */
class ConvertGroupPriceDisplayPatch implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Magento\Framework\DB\FieldDataConverterFactory
     */
    private $fieldDataConverterFactory;
    /**
     * @var \Magento\Framework\DB\Select\QueryModifierFactory
     */
    private $queryModifierFactory;
    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param \Magento\Framework\DB\FieldDataConverterFactory $fieldDataConverterFactory
     * @param \Magento\Framework\DB\Select\QueryModifierFactory $queryModifierFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        FieldDataConverterFactory $fieldDataConverterFactory,
        QueryModifierFactory $queryModifierFactory,
        Manager $moduleManager,
        LoggerInterface $logger
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->logger = $logger;
        $this->fieldDataConverterFactory = $fieldDataConverterFactory;
        $this->queryModifierFactory = $queryModifierFactory;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Must be higher than version in module.xml to be executed
     */
    public static function getVersion()
    {
        return '1.8.7';
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
     *
     * @return void
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->convertSerializedDataToJson();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Convert data
     *
     * @return void
     */
    private function convertSerializedDataToJson()
    {
        if ($this->moduleManager->isEnabled('Geissweb_Euvat')) {
            $fieldDataConverter = $this->fieldDataConverterFactory->create(
                SerializedToJson::class
            );

            $queryModifier = $this->queryModifierFactory->create('in', [ 'values' => [
                'path' => [
                    'euvat/group_price_display/catalog_price_display',
                    'euvat/group_price_display/cart_product_price_display',
                    'euvat/group_price_display/cart_subtotal_price_display'
                ]
            ]]);

            try {
                $fieldDataConverter->convert(
                    $this->moduleDataSetup->getConnection(),
                    $this->moduleDataSetup->getTable('core_config_data'),
                    'config_id',
                    'value',
                    $queryModifier
                );
            } catch (FieldDataConversionException $e) {
                $this->logger->critical($e);
            }
        }
    }
}
