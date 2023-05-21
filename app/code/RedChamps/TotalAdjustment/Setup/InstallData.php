<?php
/**
 * @author RedChamps Team
 * @copyright Copyright (c) RedChamps (https://redchamps.com/)
 * @package RedChamps_TotalAdjustment
 */
namespace RedChamps\TotalAdjustment\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;

class InstallData implements InstallDataInterface
{
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
        SalesSetupFactory $salesSetupFactory
    ) {
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }
    /**
     * Upgrades DB for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var \Magento\Quote\Setup\QuoteSetup $quoteInstaller */
        $quoteInstaller = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);

        /** @var \Magento\Sales\Setup\SalesSetup $salesInstaller */
        $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

        $setup->startSetup();

        //Add attributes to quote
        $attrCode = 'adjustments';
        $attrDef = [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> 855,
            'visible' => false,
            'nullable' => false
        ];

        foreach (['quote', 'quote_address'] as $entity) {
            $quoteInstaller->addAttribute(
                $entity,
                $attrCode,
                $attrDef
            );
        }

        foreach (['order', 'invoice', 'creditmemo'] as $entity) {
            $salesInstaller->addAttribute(
                $entity,
                $attrCode,
                $attrDef
            );
        }

        $salesInstaller->addAttribute(
            'order',
            'adjustments_invoiced',
            $attrDef
        );
        $salesInstaller->addAttribute(
            'order',
            'adjustments_refunded',
            $attrDef
        );

        $setup->endSetup();
    }
}
