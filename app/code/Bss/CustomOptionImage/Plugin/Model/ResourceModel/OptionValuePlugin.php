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
 * @copyright  Copyright (c) 2015-2022 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\CustomOptionImage\Plugin\Model\ResourceModel;

use Magento\Catalog\Model\Product\Option\Value;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;

class OptionValuePlugin
{
    /**
     * @var \Bss\CustomOptionImage\Helper\ImageSaving
     */
    private $imageSaving;

    /**
     * @var \Bss\CustomOptionImage\Model\ImageUrlFactory
     */
    private $imageUrlFactory;

    /**
     * @var \Bss\CustomOptionImage\Model\ResourceModel\ImageUrl
     */
    private $imageUrlResource;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;
    /**
     * OptionValuePlugin constructor.
     * @param \Bss\CustomOptionImage\Helper\ImageSaving $imageSaving
     * @param \Bss\CustomOptionImage\Model\ImageUrlFactory $imageUrlFactory
     * @param \Bss\CustomOptionImage\Model\ResourceModel\ImageUrl $imageUrlResource
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Bss\CustomOptionImage\Helper\ImageSaving $imageSaving,
        \Bss\CustomOptionImage\Model\ImageUrlFactory $imageUrlFactory,
        \Bss\CustomOptionImage\Model\ResourceModel\ImageUrl $imageUrlResource,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->imageSaving = $imageSaving;
        $this->imageUrlFactory = $imageUrlFactory;
        $this->imageUrlResource = $imageUrlResource;
        $this->request = $request;
    }

    /**
     * Around Plugin Save Value
     *
     * @param Value $subject
     * @param callable $proceed
     * @return void
     * @throws AlreadyExistsException
     * @throws FileSystemException
     * @throws LocalizedException|LocalizedException
     */
    public function aroundSave(
        \Magento\Catalog\Model\Product\Option\Value $subject,
        callable $proceed
    ) {
        $imageUrl = $this->imageSaving->moveImage($subject);
        $swatchImage = $this->imageSaving->moveImageSwatch($subject);
        if (!$subject->getData('bss_image_button')
            && $this->request->getFullActionName() != 'bsscustomoption_template_save'
            && !($subject->getOption() && $subject->getOption()->getData("bsscustomoption_template_save"))
        ) {
            return $proceed();
        }
        $proceed();
        $imageUrlModel = $this->imageUrlFactory->create()->loadByOptionTyeId($subject->getOptionTypeId());
        if (!$imageUrlModel) {
            $imageUrlModel = $this->imageUrlFactory->create();
        }
        if ($this->checkImage($subject)) {
            $imageUrl = $subject->getData('image_url');
        }
        if ($this->checkSwatchImage($subject)) {
            $swatchImage = $subject->getData('swatch_image_url');
        }
        $imageUrlModel->setOptionTypeId($subject->getOptionTypeId())
            ->setImageUrl($imageUrl)
            ->setSwatchImageUrl($swatchImage);
        $this->imageUrlResource->save($imageUrlModel);
    }

    /**
     * Check swatch image
     *
     * @param mixed $subject
     * @return bool
     */
    private function checkSwatchImage($subject)
    {
        return $subject->getData('swatch_image_url_hidden') =='bss'
            && $subject->getData('swatch_image_url')
            && (
                strpos($subject->getData('swatch_image_url'), 'bss/coi-image-swatch/') !== false
                || strpos($subject->getData('swatch_image_url'), 'bss/customoptiontemplate/') !== false
            );
    }

    /**
     * Check image
     *
     * @param mixed $subject
     * @return bool
     */
    private function checkImage($subject)
    {
        return $subject->getData('bss_image_button') =='bss'
            && $subject->getData('image_url')
            && (
                strpos($subject->getData('image_url'), 'bss/coi-image/') !== false
                || strpos($subject->getData('image_url'), 'bss/customoptiontemplate/') !== false
            );
    }

    /**
     * Around get data
     *
     * @param \Magento\Catalog\Model\Product\Option\Value $subject
     * @param mixed $result
     * @param string $key
     * @param int $index
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws LocalizedException
     */
    public function afterGetData(
        \Magento\Catalog\Model\Product\Option\Value $subject,
        $result,
        $key = '',
        $index = null
    ) {
        $imageUrl = $this->imageUrlFactory->create();
        if ($key === '') {
            if (isset($result['option_type_id']) && !isset($result['image_url'])) {
                $imageData = $imageUrl->getImageOptionUrl($result['option_type_id'], 'image_url');
                $result['image_url'] = $imageData;
            }
            if (isset($result['option_type_id']) && !isset($result['swatch_image_url_hidden'])) {
                $imageData = $imageUrl->getImageOptionUrl($result['option_type_id'], 'swatch_image_url');
                $result['swatch_image_url'] = $imageData;
            }
        }
        if ($this->checkIssetImageData($subject, $key)) {
            $imageData = $imageUrl->getImageOptionUrl($subject->getData('option_type_id'), 'image_url');
            return $imageData;
        }
        if ($this->checkIssetSwatchImageData($subject, $key)) {
            $imageData = $imageUrl->getImageOptionUrl(
                $subject->getData('option_type_id'),
                'swatch_image_url'
            );
            return $imageData;
        }
        return $result;
    }

    /**
     * Check isset image data
     *
     * @param mixed $subject
     * @param string $key
     * @return bool
     */
    private function checkIssetImageData($subject, $key)
    {
        return $key === 'image_url' && $subject->getData('option_type_id') && !$subject->hasData('image_url');
    }

    /**
     * Check isset swatch image data
     *
     * @param mixed $subject
     * @param string $key
     * @return bool
     */
    private function checkIssetSwatchImageData($subject, $key)
    {
        return $key === 'swatch_image_url'
            && $subject->getData('option_type_id')
            && !$subject->hasData('swatch_image_url');
    }

    /**
     * Add data to value
     *
     * @param \Magento\Catalog\Model\Product\Option\Value $subject
     * @param \Magento\Catalog\Model\ResourceModel\Product\Option\Value\Collection $result
     * @return mixed
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function afterGetValuesCollection(
        \Magento\Catalog\Model\Product\Option\Value $subject,
        $result
    ) {
        $result->getSelect()->joinLeft(
            ['opt' => $subject->getCollection()->getTable('bss_catalog_product_option_type_image')],
            'opt.option_type_id=main_table.option_type_id'
        );
        foreach ($result as $value) {
            if ($value->getImageUrl()) {
                $imageUrl = $this->imageSaving->duplicateImage($value->getImageUrl());
                $value->setImageUrl($imageUrl);
            }
            if ($value->getSwatchImageUrl()) {
                $swatchImageUrl = $this->imageSaving->duplicateImage($value->getSwatchImageUrl());
                $value->setSwatchImageUrl($swatchImageUrl);
            }
        }
        return $result;
    }
}
