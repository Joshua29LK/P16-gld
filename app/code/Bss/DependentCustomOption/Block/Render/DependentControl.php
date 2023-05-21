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
 * @package    Bss_DependentCustomOption
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\DependentCustomOption\Block\Render;

use Bss\DependentCustomOption\Helper\ModuleConfig;
use Bss\DependentCustomOption\Model\DependOptionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Bss\DependentCustomOption\Model\Config\Source\MultipleParent;

class DependentControl extends Template
{
    /**
     * @var ModuleConfig
     */
    protected $moduleConfig;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @var DependOptionFactory
     */
    protected $dependOptionFactory;

    /**
     * DependentControl constructor.
     * @param Context $context
     * @param Json $json
     * @param ModuleConfig $moduleConfig
     * @param DependOptionFactory $dependOptionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Json $json,
        ModuleConfig $moduleConfig,
        DependOptionFactory $dependOptionFactory,
        array $data = []
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->json = $json;
        $this->dependOptionFactory = $dependOptionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->setTemplate('Bss_DependentCustomOption::select/dependent-control.phtml');
    }

    /**
     * GetConfigHelper
     *
     * @return ModuleConfig
     */
    public function getConfigHelper()
    {
        return $this->moduleConfig;
    }

    /**
     * GetDependData
     *
     * @return bool|string
     */
    public function getDependData()
    {
        $result = [];
        $values = $this->getOption()->getValues();
        $result['dependent_id'] = $this->getOption()->getData('dependent_id');
        foreach ($values as $value) {
            $dataChild =  $value->getData();
            if (!$this->havingDependValue($dataChild)) {
                $dataChild = $this->modifyChildNoDepend($dataChild);
            }
            $result['child'][$value->getOptionTypeId()] = $dataChild;
        }
        if ($result['dependent_id']  !== null) {
            return $this->json->serialize($result);
        }
        return false;
    }

    /**
     * Check if option having any depend
     *
     * @param array $data
     * @return bool
     */
    public function havingDependValue($data)
    {
        if ($data['depend_value'] != '' && $data['depend_value'] != null) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMultipleParentValue()
    {
        $product = $this->getProduct();
        if (!$product->getData('depend_option_config_child_values')
            || $product->getData('depend_option_config_child_values') == MultipleParent::USE_GLOBAL_CONFIG) {
            return $this->moduleConfig->getMultipleParentValue();
        }
        return $product->getData('depend_option_config_child_values');
    }

    /**
     * Set depend id to null
     *
     * @param array $data
     * @return mixed
     */
    public function modifyChildNoDepend($data)
    {
        $data['depend_value'] = '0';
        return $data;
    }
}
