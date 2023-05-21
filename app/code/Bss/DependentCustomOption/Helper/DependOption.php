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
namespace Bss\DependentCustomOption\Helper;

/**
 * Class DependOption
 *
 * @package Bss\DependentCustomOption\Helper
 */
class DependOption
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Bss\DependentCustomOption\Model\DependOptionFactory
     */
    protected $dependOptionFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * DependOption constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Bss\DependentCustomOption\Model\DependOptionFactory $dependOptionFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Bss\DependentCustomOption\Model\DependOptionFactory $dependOptionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        $this->logger = $logger;
        $this->dependOptionFactory = $dependOptionFactory;
        $this->productFactory = $productFactory;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @param mixed $subject
     * @param mixed $result
     * @param string $type
     */
    public function saveDepend($subject, $result, $type = 'option_id')
    {
        if ($type == 'option_type_id') {
            $productId = $subject->getOption()->getProductId();
        } else {
            $productId =  $result->getProductId();
        }
        if (in_array($this->productMetadata->getEdition(), ['EE', 'B2B'])) {
            $product = $this->productFactory->create()->loadByAttribute('row_id', $productId);
            if ($product && $product->getId()) {
                $productId = $product->getId();
            }
        }
        $data = ['increment_id' => (int)$subject->getDependentId(),
            'depend_value' => $subject->getDependValue(),
            'product_id' => (int)$productId
        ];
        if ($type == 'option_id') {
            $data['option_id'] = (int)$result->getOptionId();
        }
        if ($type == 'option_type_id') {
            $data['option_type_id'] = (int)$subject->getOptionTypeId();
        }
        $this->saveDependOption($productId, $data, $subject);
    }

    /**
     * @param int $productId
     * @param mixed $data
     * @param mixed $subject
     */
    public function saveDependOption($productId, $data, $subject = null)
    {
        $dependOptionModel = $this->dependOptionFactory->create();
        $dependOptionModel->setProductId($productId);
        if ($subject) {
            $dependOptionModel->load($subject->getDependentId());
        } else {
            $dependOptionModel->load(0);
        }
        if ($dependOptionModel->getId()) {
            $dependOptionModel->addData($data);
        } else {
            $dependOptionModel->setData($data);
        }
        try {
            $dependOptionModel->save();
        } catch (\Exception $e) {
            //
            $this->logger->critical($e);
        }
    }

    /**
     * @param int $oldId
     * @param int $newId
     * @param int $productId
     * @param string $type
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function returnDependData($oldId, $newId, $productId, $type = 'option_id')
    {
        $data = [];
        if ($type == 'option_type_id') {
            $dependData = $this->dependOptionFactory->create()->loadByOptionTyeId($oldId);
            $data['option_type_id'] = $newId;
        } else {
            $dependData = $this->dependOptionFactory->create()->loadByOptionId($oldId);
            $data['option_id'] = $newId;
        }
        $dependentId = null;
        $dependValue = null;
        if ($dependData) {
            $dependentId = $dependData->getData('increment_id');
            $dependValue = $dependData->getData('depend_value');
        }
        $data = [
            'increment_id' => $dependentId,
            'depend_value' => $dependValue,
            'product_id' => $productId
        ];
        if ($type = 'option_id') {
            $data['depend_value'] = '';
        }
        return $data;
    }
}
