<?php
/**
 * MageDelight
 * Copyright (C) 2023 Magedelight <info@magedelight.com>
 *
 * @category MageDelight
 * @package Magedelight_Megamenu
 * @copyright Copyright (c) 2023 Magedelight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Megamenu\Block;

use Magento\Catalog\Helper\Data;
use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Crumbs extends Template
{
    /**
     * Catalog data
     * @var Data
     */
    private $catalogData = null;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var Context
     */
    private $context;
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var Http
     */
    private $request;
    /**
     * @var array
     */
    private $data;

    /**
     * Crumbs constructor.
     * @param Context $context
     * @param Data $catalogData
     * @param Registry $registry
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $catalogData,
        Registry $registry,
        ScopeConfigInterface $scopeConfig,
        Http $request,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->context = $context;
        $this->catalogData = $catalogData;
        $this->registry = $registry;
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->data = $data;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCrumbs()
    {
        $evercrumbs = [];
        $evercrumbs[] = [
            'label' => __('Home'),
            'title' => __('Go to Home Page'),
            'link' => $this->_storeManager->getStore()->getBaseUrl()
        ];
        $product = $this->registry->registry('current_product');
        $path = $this->catalogData->getBreadcrumbPath();
        if ($path) {
            $path = $this->catalogData->getBreadcrumbPath();
            foreach ($path as $k => $p) {
                $evercrumbs[] = [
                    'label' => $p['label'],
                    'title' => $p['label'],
                    'link' => isset($p['link']) ? $p['link'] : ''
                ];
            }
        } else {
             $evercrumbs[] = [
                'label' => $product->getName(),
                'title' => $product->getName(),
                'link' => ''
             ];
        }
        return $evercrumbs;
    }
}
