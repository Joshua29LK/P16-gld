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
 * @category   BSS
 * @package    Bss_Faqs
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\Faqs\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * Install tables
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();
        //Create bss_faqs
        if (!$installer->tableExists('bss_faqs')) {
            $table = $installer->getConnection()
                ->newTable(
                    $installer->getTable('bss_faqs')
                )
                ->addColumn(
                    'faq_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['primary' => true, 'auto_increment' => true, 'nullable' => false],
                    'Faq Id'
                )
                ->addColumn(
                    'title',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Title'
                )
                ->addColumn(
                    'category_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    [],
                    'Category Id'
                )
                ->addColumn(
                    'answer',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    ['nullable' => false],
                    'Answer'
                )
                ->addColumn(
                    'is_show_full_answer',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['nullable' => false],
                    'Is Show Full Answer'
                )
                ->addColumn(
                    'short_answer',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    [],
                    'Short answer'
                )
                ->addColumn(
                    'customer',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Customer'
                )
                ->addColumn(
                    'is_most_frequently',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['nullable' => false],
                    'Most frequently asked question : Y/N'
                )
                ->addColumn(
                    'tag',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Tag'
                )
                ->addColumn(
                    'time',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Time Created'
                )
                ->addColumn(
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['nullable' => false],
                    'Store view'
                )
                ->addColumn(
                    'related_faq_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    ['nullable' => true],
                    'Related question'
                )
                ->addColumn(
                    'url_key',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Url key for faq page'
                )
                ->addColumn(
                    'use_real_vote_data',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['nullable' => false, 'default' => '1'],
                    'Use Real Voting Data'
                )
                ->addColumn(
                    'helpful_vote',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    [],
                    'Number of helpful vote'
                )
                ->addColumn(
                    'unhelpful_vote',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    [],
                    'Number of unhelpful vote'
                )
                ->addColumn(
                    'product_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    ['nullable' => true],
                    'Product Id'
                )
                ->addColumn(
                    'is_check_all_product',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['nullable' => false],
                    'Is product link to all product'
                )
                ->addIndex(
                    $installer->getIdxName('bss_faqs', ['faq_id']),
                    ['faq_id']
                )
                ->setComment(
                    'Preselect key for configurable product'
                );
            $installer->getConnection()->createTable($table);
        }
        //Create bss_faq_category
        if (!$installer->tableExists('bss_faq_category')) {
            $table = $installer->getConnection()
                ->newTable(
                    $installer->getTable('bss_faq_category')
                )
                ->addColumn(
                    'faq_category_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['primary' => true, 'auto_increment' => true, 'nullable' => false],
                    'Faq Category Id'
                )
                ->addColumn(
                    'title',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Title'
                )
                ->addColumn(
                    'faq_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    [],
                    'FAQ Id'
                )
                ->addColumn(
                    'faq_id_to_show',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'FAQ Id Show on Mainpage'
                )
                ->addColumn(
                    'image',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Category Image'
                )
                ->addColumn(
                    'show_in_mainpage',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['nullable' => false],
                    'Show in main page: Y/N'
                )
                ->addColumn(
                    'url_key',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Url key for category page'
                )
                ->addColumn(
                    'color',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => '#cccccc'],
                    'Color'
                )
                ->addColumn(
                    'title_color',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => '#000000'],
                    'Title Color'
                )
                ->addIndex(
                    $installer->getIdxName('bss_faq_category', ['faq_category_id']),
                    ['faq_category_id']
                )
                ->addIndex(
                    $installer->getIdxName('bss_faq_category', ['url_key']),
                    ['url_key']
                )
                ->setComment(
                    'Preselect key for configurable product'
                );
            $installer->getConnection()->createTable($table);
        }
        //Create bss_faqs_voting
        if (!$installer->tableExists('bss_faqs_voting')) {
            $table = $installer->getConnection()
                ->newTable(
                    $installer->getTable('bss_faqs_voting')
                )
                ->addColumn(
                    'vote_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['primary' => true, 'auto_increment' => true, 'nullable' => false],
                    'Vote Id'
                )
                ->addColumn(
                    'faq_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['nullable' => false],
                    'Faq Id'
                )
                ->addColumn(
                    'user_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['nullable' => false],
                    'Customer Id'
                )
                ->addColumn(
                    'vote_value',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['nullable' => false],
                    'Vote value'
                )
                ->addIndex(
                    $installer->getIdxName('bss_faqs_voting', ['vote_id']),
                    ['vote_id']
                )
                ->setComment(
                    'Preselect key for configurable product'
                );
            $installer->getConnection()->createTable($table);
        }
        //Create bss_faqs_store
        if (!$installer->tableExists('bss_faqs_store')) {
            $table = $installer->getConnection()
                ->newTable(
                    $installer->getTable('bss_faqs_store')
                )
                ->addColumn(
                    'faq_store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['primary' => true, 'auto_increment' => true, 'nullable' => false],
                    'Id'
                )
                ->addColumn(
                    'faq_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['nullable' => false],
                    'Faq Id'
                )
                ->addColumn(
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['nullable' => false],
                    'Store Id'
                )
                ->addColumn(
                    'url_key',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Url key for faq page'
                )
                ->addIndex(
                    $installer->getIdxName('bss_faqs_store', ['faq_store_id']),
                    ['faq_store_id']
                )
                ->setComment(
                    'the junction table'
                );
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}
