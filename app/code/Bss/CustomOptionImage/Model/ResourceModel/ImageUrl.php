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
 * @package    Bss_CustomOptionImage
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CustomOptionImage\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ImageUrl extends AbstractDb
{
    protected $configOldImageSize = [
        'Dropdown_y',
        'Dropdown_x',
        'Radio_y',
        'Radio_x',
        'Checkbox_y',
        'Checkbox_x',
        'Multiple_y',
        'Multiple_x'
    ];

    protected $configOldFrontend = [
        'Dropdown',
        'Multiple',
    ];

    /**
     * construct
     */
    public function _construct()
    {
        $this->_init('bss_catalog_product_option_type_image', 'image_id');
    }

    /**
     * @param int $optionId
     * @param string $type
     * @return bool|mixed
     * @throws \Zend_Db_Statement_Exception
     */
    public function getImageOptionUrl($optionId, $type = 'image_url')
    {
        $connection = $this->getConnection();
        $bssOptionImg = $this->getTable('bss_catalog_product_option_type_image');
        $select = $connection->select()
            ->from(
                $bssOptionImg,
                $type
            )->where('option_type_id = ?', $optionId);

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * @param int $optionTypeId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByOptionTyeId($optionTypeId)
    {
        $bind = ['option_type_id' => $optionTypeId];
        $select = $this->getConnection()->select()->from(
            $this->getMainTable()
        )->where(
            'option_type_id = :option_type_id'
        )->limit(1);

        return $this->getConnection()->fetchRow($select, $bind);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function convertOldToNewConfig()
    {
        foreach ($this->configOldImageSize as $value) {
            if ($this->checkConfigExist('Bss_Commerce/image_size/' . $value)) {
                $this->updateOldConfig(
                    'bss_coi/image_size/' . strtolower($value),
                    'Bss_Commerce/image_size/' . $value
                );
            }
        }
        foreach ($this->configOldFrontend as $value) {
            if ($this->checkConfigExist('Bss_Commerce/frontend_view/' . $value)) {
                $this->updateOldConfig(
                    'bss_coi/frontend_view/' . strtolower($value),
                    'Bss_Commerce/frontend_view/' . $value
                );
            }
        }
        if ($this->checkConfigExist('Bss_Commerce/Customoptionimage/Enable')) {
            $this->updateOldConfig(
                'bss_coi/general/enable',
                'Bss_Commerce/Customoptionimage/Enable'
            );
        }
    }

    /**
     * @param string $oldPath
     * @return string
     */
    protected function checkConfigExist($oldPath)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('core_config_data')
        )->where('path = ?', (string)$oldPath)
            ->limit(1);
        return $this->getConnection()->fetchOne($select);
    }

    /**
     * @param string $newPath
     * @param string $oldPath
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function updateOldConfig($newPath, $oldPath)
    {
        try {
            $this->getConnection()->update(
                $this->getTable('core_config_data'),
                [
                    'path' => (string)$newPath
                ],
                ['path =?' => (string)$oldPath]
            );
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkOldModule()
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('setup_module'),
            'schema_version'
        )->where(
            'module =?',
            'Bss_CustomOptionImage'
        );
        return $this->getConnection()->fetchOne($select);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function removeOldModuleSetup()
    {
        try {
            $this->getConnection()->delete(
                $this->getTable('setup_module'),
                ['module =?' => 'Bss_CustomOptionImage']
            );
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }
    }
}
