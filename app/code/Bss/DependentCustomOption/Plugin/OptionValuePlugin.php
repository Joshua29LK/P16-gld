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
namespace Bss\DependentCustomOption\Plugin;

use Magento\Catalog\Model\Product\Option\Value;

class OptionValuePlugin
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
     * @var \Bss\DependentCustomOption\Helper\DependOption
     */
    protected $dependOption;

    /**
     * @var \Bss\DependentCustomOption\Model\ResourceModel\DependOption
     */
    protected $resourceDependOption;

    /**
     * OptionValuePlugin constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Bss\DependentCustomOption\Model\DependOptionFactory $dependOptionFactory
     * @param \Bss\DependentCustomOption\Model\ResourceModel\DependOption $resourceDependOption
     * @param \Bss\DependentCustomOption\Helper\DependOption $dependOption
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Bss\DependentCustomOption\Model\DependOptionFactory $dependOptionFactory,
        \Bss\DependentCustomOption\Model\ResourceModel\DependOption $resourceDependOption,
        \Bss\DependentCustomOption\Helper\DependOption $dependOption
    ) {
        $this->logger = $logger;
        $this->dependOptionFactory = $dependOptionFactory;
        $this->resourceDependOption = $resourceDependOption;
        $this->dependOption = $dependOption;
    }

    /**
     * AfterSave
     *
     * @param Value $subject
     * @param Value $result
     * @return Value
     */
    public function afterSave(
        Value $subject,
        $result
    ) {
        if ($subject->getDependentId()) {
            $this->dependOption->saveDepend($subject, $result, 'option_type_id');
        }
        return $result;
    }

    /**
     * After GetData
     *
     * @param Value $subject
     * @param mixed $result
     * @param string $key
     * @param string $index
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetData(
        Value $subject,
        $result,
        $key = '',
        $index = null
    ) {
        if ($key === '') {
            $result['dependent_id'] = null;
            $result['depend_value'] = null;
            if (isset($result['option_type_id'])) {
                $dependData = $this->dependOptionFactory->create()->loadByOptionTyeId($result['option_type_id']);
                if ($dependData) {
                    $result['dependent_id'] = $dependData->getData('increment_id');
                    $result['depend_value'] = $dependData->getData('depend_value');
                }
            }
            return $result;
        }
        if (($key === 'dependent_id' || $key === 'depend_value') && !$subject->hasData($key)) {
            $dependData = $this->dependOptionFactory->create()->loadByOptionTyeId($subject->getData('option_type_id'));
            $val = null;
            return $dependData ? $dependData->getData($key) : $val;
        }
        return $result;
    }
    /**
     * Add data to value
     *
     * @param \Magento\Catalog\Model\Product\Option\Value $subject
     * @param \Magento\Catalog\Model\ResourceModel\Product\Option\Value\Collection $result
     * @return mixed
     * @throws \Magento\Framework\Exception\FileSystemException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetValuesCollection(
        \Magento\Catalog\Model\Product\Option\Value $subject,
        $result
    ) {
        foreach ($result as $value) {
            $rowDepend =$this->resourceDependOption->loadByOptionTyeId($value->getId());
            if (!empty($rowDepend)) {
                $value->setDependentId($rowDepend['increment_id']);
                $value->setDependValue($rowDepend['depend_value']);
            }
        }
        return $result;
    }
}
