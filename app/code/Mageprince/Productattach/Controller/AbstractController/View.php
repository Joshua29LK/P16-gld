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

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;

abstract class View extends Action\Action
{
    /**
     * @var \Mageprince\Productattach\Controller\AbstractController\ProductattachLoaderInterface
     */
    private $productattachLoader;
    
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param OrderLoaderInterface $orderLoader
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        ProductattachLoaderInterface $productattachLoader,
        PageFactory $resultPageFactory
    ) {
        $this->productattachLoader = $productattachLoader;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Productattach view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->productattachLoader->load($this->_request, $this->_response)) {
            return;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
