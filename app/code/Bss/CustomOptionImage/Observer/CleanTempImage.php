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
namespace Bss\CustomOptionImage\Observer;

use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer as EventObserver;

class CleanTempImage implements ObserverInterface
{
    /**
     * @var \Bss\CustomOptionImage\Helper\ImageSaving
     */
    protected $imageSaving;

    /**
     * @var \Bss\CustomOptionImage\Model\ImageUrlFactory
     */
    protected $imageFactory;

    /**
     * CleanTempImage constructor.
     * @param \Bss\CustomOptionImage\Helper\ImageSaving $imageSaving
     * @param \Bss\CustomOptionImage\Model\ImageUrlFactory $imageFactory
     */
    public function __construct(
        \Bss\CustomOptionImage\Helper\ImageSaving $imageSaving,
        \Bss\CustomOptionImage\Model\ImageUrlFactory $imageFactory
    ) {
        $this->imageSaving = $imageSaving;
        $this->imageFactory = $imageFactory;
    }

    /**
     * @param EventObserver $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(EventObserver $observer)
    {
        $this->imageSaving->cleanTempFile();
    }
}
