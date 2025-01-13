<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Superiortile\RequiredProduct\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Superiortile\RequiredProduct\Model\Product\Link;
use Superiortile\RequiredProduct\Ui\DataProvider\Product\Form\Modifier\RequiredProduct;

/**
 * Class Superiortile\RequiredProduct\Helper\Data
 */
class Data extends AbstractHelper
{
    public const MAXIMUM_PRODUCT_PER_COLLECTION = 8;

    /**
     * Get Required Product Link Type Id By Code
     *
     * @param  string $linkTypeCode
     * @return mixed
     */
    public function getRequiredProductLinkTypeIdByCode($linkTypeCode = false)
    {
        $typeIds = [
            RequiredProduct::DATA_SCOPE_REQUIRED_PRODUCT_1 => Link::LINK_TYPE_REQUIRE_PRODUCT_1,
            RequiredProduct::DATA_SCOPE_REQUIRED_PRODUCT_2 => Link::LINK_TYPE_REQUIRE_PRODUCT_2,
            RequiredProduct::DATA_SCOPE_REQUIRED_PRODUCT_3 => Link::LINK_TYPE_REQUIRE_PRODUCT_3,
            RequiredProduct::DATA_SCOPE_REQUIRED_PRODUCT_4 => Link::LINK_TYPE_REQUIRE_PRODUCT_4,
            RequiredProduct::DATA_SCOPE_REQUIRED_PRODUCT_5 => Link::LINK_TYPE_REQUIRE_PRODUCT_5,
            RequiredProduct::DATA_SCOPE_REQUIRED_PRODUCT_6 => Link::LINK_TYPE_REQUIRE_PRODUCT_6,
            RequiredProduct::DATA_SCOPE_REQUIRED_PRODUCT_7 => Link::LINK_TYPE_REQUIRE_PRODUCT_7,
            RequiredProduct::DATA_SCOPE_REQUIRED_PRODUCT_8 => Link::LINK_TYPE_REQUIRE_PRODUCT_8

        ];

        if ($linkTypeCode === false) {
            return $typeIds;
        }

        if (!isset($typeIds[$linkTypeCode])) {
            throw new LocalizedException(__('Required product list with type code "%s" not exists!', $linkTypeCode));
        }

        return $typeIds[$linkTypeCode];
    }

    /**
     * Get Collection Name Attribute Code By Type Id
     *
     * @param  int $linkTypeId
     * @return string|null
     */
    public function getCollectionNameAttributeCodeByTypeId($linkTypeId)
    {
        $attributeCodes = [
            Link::LINK_TYPE_REQUIRE_PRODUCT_1 => 'require_name_1',
            Link::LINK_TYPE_REQUIRE_PRODUCT_2 => 'require_name_2',
            Link::LINK_TYPE_REQUIRE_PRODUCT_3 => 'require_name_3',
            Link::LINK_TYPE_REQUIRE_PRODUCT_4 => 'require_name_4',
            Link::LINK_TYPE_REQUIRE_PRODUCT_5 => 'require_name_5',
            Link::LINK_TYPE_REQUIRE_PRODUCT_6 => 'require_name_6',
            Link::LINK_TYPE_REQUIRE_PRODUCT_7 => 'require_name_7',
            Link::LINK_TYPE_REQUIRE_PRODUCT_8 => 'require_name_8',
        ];

        return $attributeCodes[$linkTypeId] ?? null;
    }

    /**
     * Get Tooltip Attribute Code By Type Id
     *
     * @param  int $linkTypeId
     * @return string|null
     */
    public function getTooltipAttributeCodeByTypeId($linkTypeId)
    {
        $attributeCodes = [
            Link::LINK_TYPE_REQUIRE_PRODUCT_1 => 'require_tooltip_1',
            Link::LINK_TYPE_REQUIRE_PRODUCT_2 => 'require_tooltip_2',
            Link::LINK_TYPE_REQUIRE_PRODUCT_3 => 'require_tooltip_3',
            Link::LINK_TYPE_REQUIRE_PRODUCT_4 => 'require_tooltip_4',
            Link::LINK_TYPE_REQUIRE_PRODUCT_5 => 'require_tooltip_5',
            Link::LINK_TYPE_REQUIRE_PRODUCT_6 => 'require_tooltip_6',
            Link::LINK_TYPE_REQUIRE_PRODUCT_7 => 'require_tooltip_7',
            Link::LINK_TYPE_REQUIRE_PRODUCT_8 => 'require_tooltip_8',
        ];

        return $attributeCodes[$linkTypeId] ?? null;
    }
}
