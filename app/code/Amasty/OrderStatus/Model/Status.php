<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Order Status for Magento 2
 */

namespace Amasty\OrderStatus\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;

class Status extends AbstractModel
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    public function __construct(
        Registry $coreRegistry,
        Context $context,
        ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;

        parent::__construct($context, $coreRegistry);
    }

    protected function _construct()
    {
        $this->_init(ResourceModel\Status::class);
    }

    public function beforeSave()
    {
        parent::beforeSave();

        if (!$this->getAlias()) {
            $this->setAlias($this->_generateAlias($this->getStatus()));
        }

        return $this;
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();

        /** @var Template $template */
        $template = $this->_objectManager->get(Template::class);
        $template->attachTemplates($this);

        return $this;
    }

    protected function _generateAlias($title)
    {
        $alias = trim(strtolower(preg_replace('@[^A-Za-z0-9_]@', '', $title)));

        if (strlen($alias) > 17) {
            $alias = substr($alias, 0, 17);
        }

        if (!$alias) {
            $alias = uniqid(rand(10, 99));
        }

        // need get unique alias
        $existStatuses = $this->getCollection();
        $existStatuses->addFieldToFilter('alias', $alias);

        while (0 < $existStatuses->getSize()) {
            unset($existStatuses);
            $alias = uniqid(rand(10, 99));
            $existStatuses = $this->getCollection();
            $existStatuses->addFieldToFilter('alias', $alias);
        }

        return $alias;
    }
}
