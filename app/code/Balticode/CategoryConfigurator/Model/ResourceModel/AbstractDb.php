<?php

namespace Balticode\CategoryConfigurator\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb as DefaultAbstractDb;
use Magento\Framework\Model\AbstractModel;

abstract class AbstractDb extends DefaultAbstractDb
{
    /**
     * Implode glue
     */
    const GLUE = ',';

    /**
     * @var array
     */
    protected $arrayFields = [];

    /**
     * @inheritdoc
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $this->implodeFields($object);

        return parent::_beforeSave($object);
    }

    /**
     * @param AbstractModel $object
     */
    protected function implodeFields(AbstractModel $object)
    {
        foreach ($this->arrayFields as $field) {
            $object->setData($field, $this->implodeValue($object->getData($field)));
        }
    }

    /**
     * @param $value
     * @return string
     */
    protected function implodeValue($value)
    {
        if (\is_string($value)) {
            return $value;
        }

        if (!\is_array($value)) {
            return '';
        }

        $string = \implode(self::GLUE, $value);

        return $string;
    }
}