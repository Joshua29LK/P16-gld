<?php

/**
 * MagePrince
 * Copyright (C) 2020 Mageprince <info@mageprince.com>
 *
 * @package Mageprince_Productattach
 * @copyright Copyright (c) 2020 Mageprince (http://www.mageprince.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MagePrince <info@mageprince.com>
 */

namespace Mageprince\Productattach\Model;

use Magento\Framework\Model\AbstractModel;
use Mageprince\Productattach\Model\Productattach as AttachmentModel;
use Mageprince\Productattach\Model\ResourceModel\Productattach as AttachmentResourceModel;

class Productattach extends AbstractModel
{
    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'pt_products_grid';

    /**
     * @var string
     */
    protected $_cacheTag = 'pt_products_grid';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'pt_products_grid';

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(AttachmentResourceModel::class);
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get products
     *
     * @param Productattach $object
     * @return array
     */
    public function getProducts(AttachmentModel $object)
    {
        $tbl = $this->getResource()->getTable("prince_productattach_relation");
        $select = $this->getResource()->getConnection()->select()->from(
            $tbl,
            ['product_id']
        )
        ->where(
            'productattach_id = ?',
            (int)$object->getId()
        );

        return $this->getResource()->getConnection()->fetchCol($select);
    }

    /**
     * Get products
     *
     * @param $attachmentId
     * @return array
     */
    public function getProductsByAttachmentId($attachmentId)
    {
        $tbl = $this->getResource()->getTable("prince_productattach_relation");
        $select = $this->getResource()->getConnection()->select()->from(
            $tbl,
            ['product_id']
        )
            ->where(
                'productattach_id = ?',
                (int)$attachmentId
            );

        return $this->getResource()->getConnection()->fetchCol($select);
    }
}
