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

namespace Mageprince\Productattach\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Asset\Repository;

class FileIcon extends \Magento\Ui\Component\Listing\Columns\Column
{
    private $storeManager;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $assetRepo;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        StoreManagerInterface $storeManager,
        Repository $assetRepo,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->storeManager = $storeManager;
        $this->assetRepo = $assetRepo;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $path = $this->storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ).'fileicon/tmp/icon/';

            $baseImage = $this->assetRepo->getUrl('Mageprince_Productattach::images/faq.png');
            foreach ($dataSource['data']['items'] as & $item) {
                if ($item['icon_image']) {
                    $item['icon_image' . '_style'] = 'width: 30px';
                    $item['icon_image' . '_src'] = $path.$item['icon_image'];
                    $item['icon_image' . '_alt'] = $item['icon_ext'];
                    $item['icon_image' . '_orig_src'] = $path.$item['icon_image'];
                } else {
                    $item['icon_image' . '_style'] = 'width: 30px';
                    $item['icon_image' . '_src'] = $baseImage;
                    $item['icon_image' . '_alt'] = 'Faq';
                    $item['icon_image' . '_orig_src'] = $baseImage;
                }
            }
        }

        return $dataSource;
    }
}
