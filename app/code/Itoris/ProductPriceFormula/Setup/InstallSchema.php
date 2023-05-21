<?php
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_M2_PRODUCT_PRICE_FORMULA
 * @copyright  Copyright (c) 2016 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */

namespace Itoris\ProductPriceFormula\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        
        $con = $setup->getConnection();
        $tmp = $con->fetchRow("SHOW COLUMNS FROM `{$setup->getTable('customer_group')}` where Field = 'customer_group_id'");
        $groupFkType = $tmp['Type']; //compatibility with M2.2
        
        $setup->run("
            CREATE TABLE IF NOT EXISTS {$setup->getTable('itoris_productpriceformula_conditions')} (
              `condition_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `formula_id` int(10) unsigned NOT NULL,
              `condition` text,
              `price` text NOT NULL,
              `override_weight` tinyint(3) unsigned NOT NULL,
              `weight` text NOT NULL,
              `position` int(10) unsigned NOT NULL,
              PRIMARY KEY (`condition_id`),
              KEY `formula_id` (`formula_id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
        ");
        
        $setup->run("
            CREATE TABLE IF NOT EXISTS {$setup->getTable('itoris_productpriceformula_group')} (
              `formula_id` int(10) unsigned NOT NULL,
              `group_id` {$groupFkType} NOT NULL,
              KEY `formula_id` (`formula_id`),
              KEY `group_id` (`group_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        
        $setup->run("
            CREATE TABLE IF NOT EXISTS {$setup->getTable('itoris_productpriceformula_formula')} (
              `formula_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `product_id` int(10) unsigned DEFAULT NULL,
              `name` varchar(255) NOT NULL,
              `position` int(10) unsigned DEFAULT NULL,
              `status` int(10) unsigned NOT NULL,
              `active_from` date DEFAULT NULL,
              `active_to` date DEFAULT NULL,
              `run_always` int(10) unsigned NOT NULL,
              `apply_to_total` smallint(5) unsigned NOT NULL,
              `frontend_total` smallint(5) unsigned NOT NULL,
              `disallow_criteria` text NOT NULL,
              PRIMARY KEY (`formula_id`),
              KEY `product_id` (`product_id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
        ");
        
        $setup->run("
            ALTER TABLE {$setup->getTable('itoris_productpriceformula_conditions')}
              ADD CONSTRAINT `itoris_productpriceformula_conditions_ibfk_1` FOREIGN KEY (`formula_id`) REFERENCES {$setup->getTable('itoris_productpriceformula_formula')} (`formula_id`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");
        
        $setup->run("
            ALTER TABLE {$setup->getTable('itoris_productpriceformula_group')}
              ADD CONSTRAINT `itoris_productpriceformula_group_ibfk_1` FOREIGN KEY (`formula_id`) REFERENCES {$setup->getTable('itoris_productpriceformula_formula')} (`formula_id`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `itoris_productpriceformula_group_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES {$setup->getTable('customer_group')} (`customer_group_id`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");
        
        $setup->run("
            ALTER TABLE {$setup->getTable('itoris_productpriceformula_formula')}
              ADD CONSTRAINT `itoris_productpriceformula_formula_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES {$setup->getTable('catalog_product_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");
        
        $setup->run("
            INSERT INTO {$setup->getTable('core_config_data')} (`scope`, `scope_id`, `path`, `value`) VALUES ('default', 0, 'itoris_productpriceformula/general/enabled', '1');
        ");
        
        $setup->endSetup();
    }
}