<?php
namespace Bss\CustomToolTipCO\Override;

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
use Magento\Ui\Component\Form\Element\Textarea;

class AddBackendCommonField extends \Bss\DependentCustomOption\Observer\Adminhtml\AddBackendCommonField
{
    const TOOL_TIP = 'tooltip_content';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ModuleConfig
     */
    protected $moduleConfig;

    /**
     * AddBackendCommonField constructor.
     * @param UrlInterface $urlBuilder
     * @param ModuleConfig $moduleConfig
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ModuleConfig $moduleConfig
    ) {
        parent::__construct($urlBuilder, $moduleConfig);
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
                60 => ['index' => static::BSS_DEPEND_ID, 'field' => $this->getDependentIdField(60)],
                100 => ['index' => static::TOOL_TIP, 'field' => $this->getToolTipFieldConfig(100)]
            ];
        }
        $observer->getChild()->addData($optionQtyField);
    }

    protected function getToolTipFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Tooltip content'),
                        'formElement' => Textarea::NAME,
                        'componentType' => Field::NAME,
                        'dataScope' => static::TOOL_TIP,
                        'sortOrder' => $sortOrder                    
                    ],
                ],
            ],
        ];
    }
}
