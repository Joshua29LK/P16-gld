<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Superiortile\RequiredProduct\Controller\Product;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\Response\HttpInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Superiortile\RequiredProduct\Model\RequiredConfiguration;

/**
 * Class Superiortile\RequiredProduct\Controller\Product\AbstractAjax
 */
abstract class AbstractAjax implements HttpPostActionInterface
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var Json
     */
    protected $serializer;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Http
     */
    protected $http;

    /**
     * @var RequestInterface
     */
    protected $request;
    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var RequiredConfiguration
     */
    protected $requiredConfiguration;

    /**
     * Constructor
     *
     * @param RequiredConfiguration $requiredConfiguration
     * @param ManagerInterface $messageManager
     * @param Validator $formKeyValidator
     * @param RequestInterface $request
     * @param PageFactory $resultPageFactory
     * @param Json $json
     * @param LoggerInterface $logger
     * @param Http $http
     */
    public function __construct(
        RequiredConfiguration $requiredConfiguration,
        ManagerInterface $messageManager,
        Validator $formKeyValidator,
        RequestInterface $request,
        PageFactory $resultPageFactory,
        Json $json,
        LoggerInterface $logger,
        Http $http
    ) {
        $this->requiredConfiguration = $requiredConfiguration;
        $this->messageManager = $messageManager;
        $this->formKeyValidator = $formKeyValidator;
        $this->request = $request;
        $this->resultPageFactory = $resultPageFactory;
        $this->serializer = $json;
        $this->logger = $logger;
        $this->http = $http;
    }

    /**
     * Create json response
     *
     * @param  array $response
     * @return Http|HttpInterface
     */
    public function jsonResponse($response = '')
    {
        $this->http->getHeaders()->clearHeaders();
        $this->http->setHeader('Content-Type', 'application/json');
        return $this->http->setBody(
            $this->serializer->serialize($response)
        );
    }
}
