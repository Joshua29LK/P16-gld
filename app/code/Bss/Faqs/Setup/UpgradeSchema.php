<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_Customoptionimage
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2016 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\Faqs\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrade
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->modifyUrlColumn($setup);
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->modifyStoreIdColumn($setup);
        }
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->addFrontendColumn($setup);
        }
    }

    /**
     * Add frontend column
     *
     * @param SchemaSetupInterface $setup
     */
    protected function addFrontendColumn($setup)
    {
        $setup->startSetup();
        $bssFaqCateTable = $setup->getTable('bss_faq_category');
        $bssFaqTable = $setup->getTable('bss_faqs');
        $connection = $setup->getConnection();
        if ($connection->isTableExists($bssFaqCateTable)) {
            $connection->addColumn(
                $setup->getTable('bss_faq_category'),
                'frontend_label',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Frontend label for different store view'
                ]
            );
        }
        if ($connection->isTableExists($bssFaqTable)) {
            $connection->addColumn(
                $setup->getTable('bss_faqs'),
                'frontend_label',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Frontend label for different store view'
                ]
            );
        }

        $setup->endSetup();
    }

    /**
     * Modify url column
     *
     * @param SchemaSetupInterface $setup
     */
    public function modifyUrlColumn($setup)
    {
        $setup->startSetup();

        $bssFaqCateTable = $setup->getTable('bss_faq_category');
        $connection = $setup->getConnection();
        if ($connection->isTableExists($bssFaqCateTable)) {
            $connection->modifyColumn(
                $bssFaqCateTable,
                'url_key',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Category Url'
                ]
            );
        }
        $setup->endSetup();
    }

    /**
     * Modify store id column
     *
     * @param SchemaSetupInterface $setup
     */
    public function modifyStoreIdColumn($setup)
    {
        $setup->startSetup();

        $bssFaqTable = $setup->getTable('bss_faqs');
        $connection = $setup->getConnection();
        if ($connection->isTableExists($bssFaqTable)) {
            $connection->modifyColumn(
                $bssFaqTable,
                'store_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Store ID'
                ]
            );
        }
        $setup->endSetup();
    }
}
