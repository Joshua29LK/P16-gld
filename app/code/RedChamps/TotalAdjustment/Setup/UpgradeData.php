<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Setup;

use Magento\Framework\DB\DataConverter\SerializedToJson;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\FieldDataConverterFactory;
use Magento\Framework\DB\Query\Generator;
use Magento\Framework\DB\Select\QueryModifierFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * Upgrade the TotalAdjustment module DB schema
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var FieldDataConverterFactory
     */
    private $fieldDataConverterFactory;

    /**
     * @var QueryModifierFactory
     */
    private $queryModifierFactory;

    /**
     * @var Generator
     */
    private $queryGenerator;

    /**
     * @var QuoteSetupFactory
     */
    protected $quoteSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory,
        FieldDataConverterFactory $fieldDataConverterFactory,
        QueryModifierFactory $queryModifierFactory,
        Generator $queryGenerator
    ) {
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->fieldDataConverterFactory = $fieldDataConverterFactory;
        $this->queryModifierFactory = $queryModifierFactory;
        $this->queryGenerator = $queryGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.9', '<')) {
            /** @var \Magento\Quote\Setup\QuoteSetup $quoteInstaller */
            $quoteInstaller = $this->quoteSetupFactory->create(
                ['resourceName' => 'quote_setup', 'setup' => $setup]
            );

            /** @var \Magento\Sales\Setup\SalesSetup $salesInstaller */
            $salesInstaller = $this->salesSetupFactory->create(
                ['resourceName' => 'sales_setup', 'setup' => $setup]
            );

            //Add attributes to quote
            $attrCodes = ['adjustments_tax', 'base_adjustments_tax', 'adjustments_tax_percentage'];
            $attrDef = [
                'type' => Table::TYPE_DECIMAL,
                'length'=> '12,4',
                'default' => 0
            ];

            foreach ($attrCodes as $attrCode) {
                $quoteInstaller->addAttribute(
                    'quote_address',
                    $attrCode,
                    $attrDef
                );

                $salesInstaller->addAttribute(
                    'order',
                    $attrCode,
                    $attrDef
                );
            }

            $salesInstaller->addAttribute(
                'order',
                'adjustments_tax_invoiced',
                $attrDef
            );

            $salesInstaller->addAttribute(
                'order',
                'base_adjustments_tax_invoiced',
                $attrDef
            );

            $salesInstaller->addAttribute(
                'order',
                'adjustments_tax_refunded',
                $attrDef
            );

            $salesInstaller->addAttribute(
                'order',
                'base_adjustments_tax_refunded',
                $attrDef
            );
        }

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->convertSerializedDataToJson($setup);
        }

        $setup->endSetup();
    }

    /*
     * from serialized to JSON format
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function convertSerializedDataToJson(ModuleDataSetupInterface $setup)
    {
        $fieldDataConverter = $this->fieldDataConverterFactory->create(
            SerializedToJson::class
        );
        $table = $setup->getTable('sales_order');
        $fieldDataConverter->convert(
            $setup->getConnection(),
            $table,
            'entity_id',
            'adjustments'
        );

        $fieldDataConverter->convert(
            $setup->getConnection(),
            $table,
            'entity_id',
            'adjustments_invoiced'
        );

        $fieldDataConverter->convert(
            $setup->getConnection(),
            $table,
            'entity_id',
            'adjustments_refunded'
        );
    }
}
