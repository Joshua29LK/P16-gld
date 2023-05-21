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

namespace Mageprince\Productattach\Block\Adminhtml\Productattach\Renderer;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageprince\Productattach\Model\Config\DefaultConfig;
use Magento\Framework\Filesystem\Io\File;

class FileIconAdmin extends AbstractElement
{
    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var \Mageprince\Productattach\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Mageprince\Productattach\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var DefaultConfig
     */
    protected $defaultConfig;

    /**
     * @var File
     */
    protected $file;

    /**
     * FileIconAdmin constructor.
     *
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Mageprince\Productattach\Helper\Data $dataHelper
     * @param \Magento\Backend\Helper\Data $helper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Registry $registry
     * @param DefaultConfig $defaultConfig
     * @param File $file
     */
    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Mageprince\Productattach\Helper\Data $dataHelper,
        \Magento\Backend\Helper\Data $helper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Registry $registry,
        DefaultConfig $defaultConfig,
        File $file
    ) {
        $this->dataHelper = $dataHelper;
        $this->assetRepo = $assetRepo;
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
        $this->coreRegistry = $registry;
        $this->defaultConfig = $defaultConfig;
        $this->file = $file;
    }

    /**
     * Get customer group name
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getElementHtml()
    {
        $fileIcon = '';
        $file = $this->getValue();
        if ($file) {
            $pathInfo = $this->file->getPathInfo($file);

            $fileIcon = '<div>';
            if (isset($pathInfo['extension'])) {
                $fileExt = $pathInfo['extension'];
                if (in_array($fileExt, $this->defaultConfig->getDefaultIcons())) {
                    $iconImage = $this->assetRepo->getUrl(
                        'Mageprince_Productattach::images/'.$fileExt.'.png'
                    );
                } else {
                    $iconImage = $this->assetRepo->getUrl(DefaultConfig::UNKNOWN_IMAGE_PATH);
                }
                $url = $this->dataHelper->getBaseUrl().$file;
                $fileIcon .= "<a href=".$url." target='_blank'>
                    <img src='".$iconImage."' style='float: left;' />
                    <div>OPEN FILE</div></a>";
            } else {
                 $iconImage = $this->assetRepo->getUrl(DefaultConfig::UNKNOWN_IMAGE_PATH);
                 $fileIcon .= "<img src='".$iconImage."' style='float: left;' />";
            }
            $attachId = $this->coreRegistry->registry('productattach_id');
            $fileIcon .= "<a id='deletefile' href='".$this->urlBuilder->getUrl(
                'productattach/index/deletefile',
                $param = ['productattach_id' => $attachId]
            )."'>
                <div style='color:red;'>DELETE FILE</div></a>";
            $fileIcon .= '</div>';
        }
        $fileUploadedContent = '<div id="uploaded-file-content"></div>';
        return $fileUploadedContent . $fileIcon;
    }
}
