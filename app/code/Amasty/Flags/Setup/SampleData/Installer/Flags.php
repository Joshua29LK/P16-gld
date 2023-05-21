<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/
declare(strict_types=1);

namespace Amasty\Flags\Setup\SampleData\Installer;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\SampleData\InstallerInterface;

class Flags implements InstallerInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public function install()
    {
        $this->moduleDataSetup->startSetup();

        $connection = $this->moduleDataSetup->getConnection();
        $connection->insert(
            $this->moduleDataSetup->getTable('amasty_flags_column'),
            [
                'id' => 1,
                'name' => __('Flags'),
                'position' => 1
            ]
        );

        $flags = [
            1 => __('Red'),
            2 => __('Orange'),
            3 => __('Green'),
            4 => __('Gray'),
            5 => __('Yellow'),
            6 => __('Blue'),
            7 => __('Black'),

            8 => __('Red'),
            9 => __('Orange'),
            10 => __('Green'),
            11 => __('Gray'),
            12 => __('Yellow'),
            13 => __('Blue'),
            14 => __('Black'),

            15 => __('Busy'),
            16 => __('Star'),
            17 => __('Plus'),
            18 => __('Plus'),
            19 => __('On Hold'),
            20 => __('On Hold'),
            21 => __('Busy'),
            22 => __('Edit'),
            23 => __('Edit'),
            24 => __('Ok'),
            25 => __('Ok'),
            26 => __('Lock'),
            27 => __('Lock'),
            28 => __('Star'),
        ];

        $flagsData = [];

        foreach ($flags as $id => $name) {
            $flagsData[] = [
                'id' => $id,
                'name' => $name,
                'priority' => $id * 10,
                'image_name' => $id . '.png'
            ];
        }

        $connection->insertMultiple(
            $this->moduleDataSetup->getTable('amasty_flags_flag'),
            $flagsData
        );

        $connection->insertMultiple(
            $this->moduleDataSetup->getTable('amasty_flags_flag_column'),
            [
                ['flag_id' => 1, 'column_id' => 1],
                ['flag_id' => 2, 'column_id' => 1],
                ['flag_id' => 3, 'column_id' => 1],
                ['flag_id' => 4, 'column_id' => 1],
                ['flag_id' => 5, 'column_id' => 1]
            ]
        );

        $this->moduleDataSetup->endSetup();
    }
}
