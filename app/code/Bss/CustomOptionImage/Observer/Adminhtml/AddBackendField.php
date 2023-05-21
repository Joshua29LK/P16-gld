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
namespace Bss\CustomOptionImage\Observer\Adminhtml;

use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer as EventObserver;
use Magento\Ui\Component\Form\Element\Hidden;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Framework\UrlInterface;

class AddBackendField implements ObserverInterface
{
    const FIELD_UPLOAD_IMAGE_PREVIEW = 'image_url';
    const FIELD_UPLOAD_IMAGE_BUTTON = 'bss_image_button';
    const FIELD_UPLOAD_SWATCH_IMAGE_BUTTON = 'swatch_image_url';
    const FIELD_UPLOAD_SWATCH_IMAGE_BUTTON_DATA = 'swatch_image_url_hidden';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * AddBackendField constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder
    ) {
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param EventObserver $observer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $observer->getChild()->addData($this->getCustomImageField());
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getCustomImageField()
    {
        return [
            230 => ['index' => static::FIELD_UPLOAD_IMAGE_PREVIEW, 'field' => $this->getImagePreviewFieldConfig(230)],
            240 => ['index' => static::FIELD_UPLOAD_IMAGE_BUTTON, 'field' => $this->getUploadButtonFieldConfig(240)],
            255 => ['index' => static::FIELD_UPLOAD_SWATCH_IMAGE_BUTTON, 'field' => $this->getSwatchImageConfig(255)],
            260 => [
                'index' => static::FIELD_UPLOAD_SWATCH_IMAGE_BUTTON_DATA,
                'field' => $this->getSwatchImageConfigHidden(260)
            ]
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getSwatchImageConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Swatch Image'),
                        'componentType' => Field::NAME,
                        'component' => 'Bss_CustomOptionImage/js/swatch_image',
                        'elementTmpl' => 'Bss_CustomOptionImage/swatch_image',
                        'dataScope' => static::FIELD_UPLOAD_SWATCH_IMAGE_BUTTON,
                        'dataType' => Text::NAME,
                        'formElement' => 'input',
                        'sortOrder' => $sortOrder,
                        'mediaUrl' => $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA),
                        'baseUrl' => $this->urlBuilder->getUrl('bss_coi/json/uploader'),
                        'valueMap' => [
                            'true' => '1',
                            'false' => ''
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getSwatchImageConfigHidden($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'dataScope' => static::FIELD_UPLOAD_SWATCH_IMAGE_BUTTON_DATA,
                        'dataType' => Text::NAME,
                        'formElement' => Hidden::NAME,
                        'sortOrder' => $sortOrder,
                        'visible' => false,
                        'value' => 'bss'
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getImagePreviewFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Image'),
                        'componentType' => Field::NAME,
                        'component' => 'Bss_CustomOptionImage/js/image_preview',
                        'elementTmpl' => 'Bss_CustomOptionImage/image-preview',
                        'dataScope' => static::FIELD_UPLOAD_IMAGE_PREVIEW,
                        'dataType' => Text::NAME,
                        'formElement' => 'input',
                        'sortOrder' => $sortOrder,
                        'mediaUrl' => $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA),
                        'baseUrl' => $this->urlBuilder->getUrl('bss_coi/json/uploader'),
                        'valueMap' => [
                            'true' => '1',
                            'false' => ''
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getUploadButtonFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'dataScope' => static::FIELD_UPLOAD_IMAGE_BUTTON,
                        'dataType' => Text::NAME,
                        'formElement' => Hidden::NAME,
                        'sortOrder' => $sortOrder,
                        'visible' => false,
                        'value' => 'bss'
                    ],
                ],
            ],
        ];
    }
}
