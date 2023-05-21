<?php

namespace MageArray\Customprice\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Csvprice extends AbstractDb
{
    /**
     * [$_csvtable description]
     *
     * @var [type]
     */
    protected $_csvtable;

    /**
     * @var null
     */
    protected $store = null;

    /**
     * @var null
     */
    protected $connection = null;

    /**
     * [_construct description]
     *
     * @return [type] [description]
     */
    protected function _construct()
    {
        $this->_init('magearray_csvprice', 'id');
        $this->_csvtable = $this->getTable('magearray_csvprice');
    }

    /**
     * [__construct description]
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context  [description]
     * @param \Magento\Framework\App\ResourceConnection         $resource [description]
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_resource = $resource;
        parent::__construct($context);
    }

    /**
     * [addCsvFilter description]
     *
     * @param [type] $product_id [description]
     * @param string $option_sku [description]
     */
    public function addCsvFilter($product_id, $option_sku = '')
    {
        $connection = $this->getConnection();
        if ($option_sku) {
            $select = $connection->select()
                    ->from(['o' =>  $this->_csvtable])
                    ->where('o.product_id=?', $product_id)
                    ->where('o.option_sku=?', $option_sku);
        } else {
            $select = $connection->select()
                    ->from(['o' =>  $this->_csvtable])
                    ->where('o.product_id=?', $product_id);
        }
        return $connection->fetchRow($select);
    }
}
