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

use Bss\CustomOptionImage\Model\ImageUrlFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class RebuildUrlV106 implements DataPatchInterface
{
    /**
     * @var ImageUrlFactory
     */
    private $imageUrlFactory;

    /**
     * RebuildUrlV106 constructor.
     *
     * @param ImageUrlFactory $imageUrlFactory
     */
    public function __construct(ImageUrlFactory $imageUrlFactory)
    {
        $this->imageUrlFactory = $imageUrlFactory;
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
     * Rebuild Url Version 1.0.6
     *
     * @return RebuildUrlV106|void
     */
    public function apply()
    {
        $this->imageUrlFactory->create()->getCollection()->walk('rebuildUrl');
    }

    /**
     * Get Version
     *
     * @return string
     */
    public static function getVersion()
    {
        return '1.0.6';
    }
}
