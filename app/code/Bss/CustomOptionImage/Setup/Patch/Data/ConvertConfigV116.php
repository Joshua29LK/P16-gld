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
namespace Bss\CustomOptionImage\Setup\Patch\Data;

use Bss\CustomOptionImage\Model\ResourceModel\ImageUrl;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class ConvertConfigV116 implements DataPatchInterface
{
    /**
     * @var ImageUrl
     */
    private $imageUrlResource;

    /**
     * ConvertConfigV116 constructor.
     *
     * @param ImageUrl $imageUrlResource
     */
    public function __construct(ImageUrl $imageUrlResource)
    {
        $this->imageUrlResource = $imageUrlResource;
    }

    /**
     * Get Dependencies
     *
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get Aliases
     *
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Convert Old To New Config Version 1.1.6
     *
     * @return ConvertConfigV116|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply()
    {
        $this->imageUrlResource->convertOldToNewConfig();
    }

    /**
     * Get Version
     *
     * @return string
     */
    public static function getVersion()
    {
        return '1.1.6';
    }
}
