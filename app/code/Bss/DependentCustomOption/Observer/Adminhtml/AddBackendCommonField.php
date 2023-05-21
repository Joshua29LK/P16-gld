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
namespace Bss\DependentCustomOption\Observer\Adminhtml;

use Bss\DependentCustomOption\Helper\ModuleConfig;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Hidden;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;

class AddBackendCommonField implements ObserverInterface
{
    const BSS_DEPEND_ID = 'dependent_id';

    const BSS_DCO_REQUIRE = 'bss_dco_require';

    const IS_REQUIRE = 'is_require';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * AddBackendCommonField constructor.
     * @param UrlInterface $urlBuilder
     * @param ModuleConfig $moduleConfig
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ModuleConfig $moduleConfig
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * Execute
     *
     * @param Observer $observer
     * @return void
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $optionQtyField = [];
        if ($this->moduleConfig->isModuleEnable()) {
            $optionQtyField = [
                60 => ['index' => static::BSS_DEPEND_ID, 'field' => $this->getDependentIdField(60)]
            ];
        }
        $observer->getChild()->addData($optionQtyField);
    }

    /**
     * GetDependentIdField
     *
     * @param int $sortOrder
     * @return array
     */
    protected function getDependentIdField($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Input::NAME,
                        'componentType' => Field::NAME,
                        'label' => __(' '),
                        'labelVisible' => true,
                        'component' => 'Bss_DependentCustomOption/js/depend-id-control',
                        'elementTmpl' => 'Bss_DependentCustomOption/depend-id',
                        'dataScope' => static::BSS_DEPEND_ID,
                        'dataType' => Number::NAME,
                        'sortOrder' => $sortOrder,
                        'jsonUrl' => $this->urlBuilder->getUrl('bss_dco/json/generator'),
                        'additionalClasses' => 'check-depend-is-parent-option',
                    ],
                ],
            ],
        ];
    }
}
