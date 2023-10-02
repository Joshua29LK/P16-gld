<?php

namespace Fooman\PdfCustomiserAmOrderAttr\Model;

use Amasty\Orderattr\Model\Entity\EntityResolver;
use Amasty\Orderattr\Model\Value\Metadata\FormFactory;

use Magento\Sales\Model\Order;
use Amasty\Orderattr\Model\Value\Metadata\Form;

class AmOrderAttributes
{
    /**
     * @var FormFactory
     */
    private $metadataFormFactory;

    /**
     * @var EntityResolver
     */
    private $entityResolver;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    public function __construct(
        FormFactory $metadataFormFactory,
        EntityResolver $entityResolver,
        \Magento\Framework\Escaper $escaper
    ) {
        $this->metadataFormFactory = $metadataFormFactory;
        $this->entityResolver = $entityResolver;
        $this->escaper = $escaper;
    }


    public function getOrderAttributesData($order)
    {
        $orderAttributesData = [];
        $filterAttributeCode = 'klant_referentie'; // Define the attribute code here
        $entity = $this->entityResolver->getEntityByOrder($order);
        if ($entity->isObjectNew()) {
            return [];
        }
        $form = $this->createEntityForm($entity, $order);
        $outputData = $form->outputData(\Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_HTML);
    
        foreach ($outputData as $attributeCode => $data) {
            if ($attributeCode === $filterAttributeCode && !empty($data)) {
                $attribute = $form->getAttribute($attributeCode);
                $orderAttributesData[] = [
                    'label' => $attribute->getDefaultFrontendLabel(),
                    'value' => ($attribute->getFrontendInput() === 'html')
                        ? $data
                        : nl2br($this->escaper->escapeHtml($data))
                ];
            }
        }
    
        return $orderAttributesData;
    }

    protected function createEntityForm($entity, $order)
    {
        /** @var Form $formProcessor */
        $formProcessor = $this->metadataFormFactory->create();
        $formProcessor->setFormCode('frontend_order_view')
            ->setEntity($entity)
            ->setStore($order->getStore());

        return $formProcessor;
    }
}
