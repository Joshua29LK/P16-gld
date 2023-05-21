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

namespace Bss\CustomOptionImage\Model;

use Magento\Framework\Model\AbstractModel;

class ImageUrl extends AbstractModel
{
    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init(\Bss\CustomOptionImage\Model\ResourceModel\ImageUrl::class);
    }

    /**
     * @throws \Exception
     */
    public function rebuildUrl()
    {
        try {
            $oldUrl = $this->getImageUrl();
            $cutStart = strpos($oldUrl, 'pub/media/');
            if ($cutStart !== false) {
                $newUrl = substr($oldUrl, $cutStart + 10);
                $this->setImageUrl($newUrl);
                $this->save();
            }
        } catch (\Exception $exception) {
            throw new \LogicException(__($exception->getMessage()));
        }
    }

    /**
     * @param int $optionId
     * @param string $type
     * @return int|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getImageOptionUrl($optionId, $type)
    {
        return $this->_getResource()->getImageOptionUrl($optionId, $type);
    }

    /**
     * @param int $optionTypeId
     * @return ImageUrl
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByOptionTyeId($optionTypeId)
    {
        $data = $this->_getResource()->loadByOptionTyeId($optionTypeId);
        return $data ? $this->setData($data) : $this;
    }
}
