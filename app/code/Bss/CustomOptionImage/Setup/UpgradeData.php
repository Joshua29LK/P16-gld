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
namespace Bss\CustomOptionImage\Setup;

use Bss\CustomOptionImage\Model\ImageUrlFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var ImageUrlFactory
     */
    private $imageUrlFactory;

    /**
     * @var \Bss\CustomOptionImage\Model\ResourceModel\ImageUrl
     */
    private $imageUrlResource;

    /**
     * UpgradeData constructor.
     * @param ImageUrlFactory $imageUrlFactory
     * @param \Bss\CustomOptionImage\Model\ResourceModel\ImageUrl $imageUrlResource
     */
    public function __construct(
        ImageUrlFactory $imageUrlFactory,
        \Bss\CustomOptionImage\Model\ResourceModel\ImageUrl $imageUrlResource
    ) {
        $this->imageUrlFactory = $imageUrlFactory;
        $this->imageUrlResource = $imageUrlResource;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.6') < 0) {
            $this->rebuildUrl();
        }
        if (version_compare($context->getVersion(), '1.1.6', '<')) {
            $this->imageUrlResource->convertOldToNewConfig();
        }

        $setup->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public function rebuildUrl()
    {
        $this->imageUrlFactory->create()->getCollection()->walk('rebuildUrl');
    }
}
