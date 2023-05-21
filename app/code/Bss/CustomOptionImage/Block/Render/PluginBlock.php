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

namespace Bss\CustomOptionImage\Block\Render;

use Magento\Framework\UrlInterface;

class PluginBlock extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Bss\CustomOptionImage\Helper\ModuleConfig
     */
    protected $moduleConfig;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * @var \Bss\CustomOptionImage\Helper\ImageSaving
     */
    protected $imageSaving;

    /**
     * @var null
     */
    protected $baseMediaUrl = null;

    /**
     * PluginBlock constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Bss\CustomOptionImage\Helper\ModuleConfig $moduleConfig
     * @param \Bss\CustomOptionImage\Helper\ImageSaving $imageSaving
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Bss\CustomOptionImage\Helper\ModuleConfig $moduleConfig,
        \Bss\CustomOptionImage\Helper\ImageSaving $imageSaving,
        \Magento\Framework\Serialize\Serializer\Json $json,
        array $data = []
    ) {
        $this->storeManager = $context->getStoreManager();
        $this->moduleConfig = $moduleConfig;
        $this->imageSaving = $imageSaving;
        $this->json = $json;
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->setTemplate('Bss_CustomOptionImage::select/image-render.phtml');
    }

    /**
     * @return \Bss\CustomOptionImage\Helper\ModuleConfig
     */
    public function getConfigHelper()
    {
        return $this->moduleConfig;
    }

    /**
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseMediaUrl()
    {
        if (!$this->baseMediaUrl) {
            $this->baseMediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        }
        return $this->baseMediaUrl;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageList()
    {
        $result = [];
        $values = $this->getOption()->getValues();
        foreach ($values as $value) {
            $valueData = $value->getData();
            if ($valueData['image_url']) {
                $image = $this->getImageUrl($valueData['image_url']);
                $result[] = [
                    'id' => $valueData['option_type_id'],
                    'url' => $image,
                    'title' => $value['title']
                ];
            }
        }
        return $this->json->serialize($result);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSwatchImage()
    {
        $result = [];
        $values = $this->getOption()->getValues();
        foreach ($values as $value) {
            $valueData = $value->getData();
            if ($valueData['swatch_image_url']) {
                $result[$value->getOptionTypeId()][] = [
                    'thumb' => $this->imageSaving->getMediaBaseUrl().$valueData['swatch_image_url'],
                    'img' => $this->imageSaving->getMediaBaseUrl().$valueData['swatch_image_url'],
                    'full' => $this->imageSaving->getMediaBaseUrl().$valueData['swatch_image_url'],
                    'caption' => '',
                    'position' => 0,
                    'isMain' => false,
                    'type' => 'image',
                    'videoUrl' => null,
                    'option_id' => $this->getOption()->getId()
                ];
            }
        }
        return $this->json->serialize($result);
    }

    /**
     * @param string $image
     * @param null $imageWidth
     * @param null $imageHeight
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageUrl($image, $imageWidth = null, $imageHeight = null)
    {
        if (!$imageWidth) {
            $imageWidth = $this->moduleConfig->getImageX($this->getOption()->getType());
        }
        if (!$imageHeight) {
            $imageHeight = $this->moduleConfig->getImageY($this->getOption()->getType());
        }

        $image = $this->imageSaving->resize($image, $imageWidth, $imageHeight);
        return $image;
    }
}
