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

use Mageprince\Productattach\Model\Config\DefaultConfig;

class FileType extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $assetRepo;

    /**
     * @var DefaultConfig
     */
    private $defaultConfig;

    /**
     * FileType constructor.
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param DefaultConfig $defaultConfig
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        DefaultConfig $defaultConfig,
        array $components = [],
        array $data = []
    ) {
        $this->assetRepo = $assetRepo;
        $this->defaultConfig = $defaultConfig;
        parent::__construct($context, $uiComponentFactory, $components, $data);
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
            foreach ($dataSource['data']['items'] as & $item) {
                $fileExt = $item['file_ext'];
                if (in_array($fileExt, $this->defaultConfig->getDefaultIcons())) {
                    $iconImageUrl = $this->assetRepo->getUrl(
                        'Mageprince_Productattach::images/'.$fileExt.'.png'
                    );
                    $item['file_ext'] = "<img src='".$iconImageUrl."' />";
                } else {
                    $iconImageUrl = $this->assetRepo->getUrl(
                        'Mageprince_Productattach::images/unknown.png'
                    );
                    $item['file_ext'] = "<img src='".$iconImageUrl."' />";
                }
            }
        }
        return $dataSource;
    }
}
