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

use Magento\Framework\Exception\NoSuchEntityException;

class Download extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Mageprince\Productattach\Helper\Data
     */
    protected $helper;

    /**
     * Download constructor.
     *
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Mageprince\Productattach\Helper\Data $helper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Mageprince\Productattach\Helper\Data $helper,
        array $components = [],
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['url'])) {
                    $url = $item['file'];
                    if ((strpos($url, 'http://') !== 0) &&
                        (strpos($url, 'https://') !== 0)) {
                        $url = 'http://' . $url;
                    }
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'target' => '_blank',
                            'href' => $url,
                            'label' => __('Goto URL')
                        ]
                    ];
                } elseif (isset($item['file'])) {
                    $url = $this->helper->getBaseUrl().$item['file'];
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'target' => '_blank',
                            'href' => $url,
                            'label' => __('Download')
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}
