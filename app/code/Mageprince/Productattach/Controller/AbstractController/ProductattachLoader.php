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

namespace Mageprince\Productattach\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class ProductattachLoader implements ProductattachLoaderInterface
{
    /**
     * @var \Mageprince\Productattach\Model\ProductattachFactory
     */
    private $productattachFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;

    /**
     * ProductattachLoader constructor.
     *
     * @param \Mageprince\Productattach\Model\ProductattachFactory $productattachFactory
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Mageprince\Productattach\Model\ProductattachFactory $productattachFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->productattachFactory = $productattachFactory;
        $this->registry = $registry;
        $this->url = $url;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return bool
     */
    public function load(RequestInterface $request, ResponseInterface $response)
    {
        $id = (int)$request->getParam('id');
        if (!$id) {
            $request->initForward();
            $request->setActionName('noroute');
            $request->setDispatched(false);
            return false;
        }

        $productattach = $this->productattachFactory->create()->load($id);
        $this->registry->register('current_productattach', $productattach);
        return true;
    }
}
