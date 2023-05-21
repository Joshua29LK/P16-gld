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

namespace Bss\CustomOptionImage\Controller\Adminhtml\Json;

use Magento\Backend\App\Action\Context;

class Uploader extends \Magento\Backend\App\Action
{
    /**
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Bss_CustomOptionImage::config';

    /**
     * @var \Bss\CustomOptionImage\Helper\ImageSaving
     */
    private $imageSaving;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * Uploader constructor.
     * @param Context $context
     * @param \Bss\CustomOptionImage\Helper\ImageSaving $imageSaving
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        \Bss\CustomOptionImage\Helper\ImageSaving $imageSaving,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->imageSaving = $imageSaving;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $param = $this->getRequest()->getParams();
            $result = $this->imageSaving->saveTemporaryImage($param['option_sortorder'], $param['value_sortorder']);
            return $resultJson->setData($result);
        } else {
            return $resultJson->setData(null);
        }
    }
}
