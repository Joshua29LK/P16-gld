<?php
/**
 * Copyright © 2019 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\Channable\Controller\Order;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magmodules\Channable\Helper\General as GeneralHelper;
use Magmodules\Channable\Helper\Order as OrderHelper;
use Magmodules\Channable\Model\Order as OrderModel;

/**
 * Class Status
 *
 * @package Magmodules\Channable\Controller\Order
 */
class Status extends Action
{

    /**
     * @var GeneralHelper
     */
    private $generalHelper;
    /**
     * @var OrderHelper
     */
    private $orderHelper;
    /**
     * @var OrderModel
     */
    private $orderModel;
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * Status constructor.
     *
     * @param Context       $context
     * @param GeneralHelper $generalHelper
     * @param OrderHelper   $orderHelper
     * @param OrderModel    $orderModel
     * @param JsonFactory   $resultJsonFactory
     */
    public function __construct(
        Context $context,
        GeneralHelper $generalHelper,
        OrderHelper $orderHelper,
        OrderModel $orderModel,
        JsonFactory $resultJsonFactory
    ) {
        $this->generalHelper = $generalHelper;
        $this->orderHelper = $orderHelper;
        $this->orderModel = $orderModel;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Execute function for Channable JSON output
     */
    public function execute()
    {
        $token = $this->generalHelper->getToken();
        $code = $this->getRequest()->getParam('code');
        if ($token && $code) {
            if ($code == $token) {
                if ($id = $this->getRequest()->getParam('id')) {
                    $response = $this->orderModel->getOrderById($id);
                } else {
                    $response = $this->orderHelper->jsonResponse('Missing ID');
                }
            } else {
                $response = $this->orderHelper->jsonResponse('Unknown Token');
            }
        } else {
            $response = $this->orderHelper->jsonResponse('Extension not enabled!');
        }

        $result = $this->resultJsonFactory->create();
        return $result->setData($response);
    }
}
