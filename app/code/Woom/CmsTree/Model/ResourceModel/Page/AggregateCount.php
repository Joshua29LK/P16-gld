<?php

namespace Woom\CmsTree\Model\ResourceModel\Page;

use Woom\CmsTree\Model\Page\Tree;
use Woom\CmsTree\Model\ResourceModel\Page\Tree as TreeResource;
use Woom\CmsTree\Api\Data\TreeInterface;
use Magento\Framework\Exception\LocalizedException;

class AggregateCount
{
    /**
     * Rebuild child count after tree delete
     *
     * @param Tree $tree
     *
     * @return void
     *
     * @throws LocalizedException
     */
    public function processDelete(Tree $tree)
    {
        /** @var TreeResource $resourceModel */
        $resourceModel = $tree->getResource();
        $parentIds = $tree->getParentIds();
        if ($parentIds) {
            $childDecrease = $tree->getChildrenCount() + 1;
            // +1 is itself
            $data = [
                TreeInterface::CHILDREN_COUNT => new \Zend_Db_Expr(
                    TreeInterface::CHILDREN_COUNT . ' - ' . $childDecrease
                )
            ];
            $where = [TreeInterface::TREE_ID . ' IN(?)' => $parentIds];
            $resourceModel->getConnection()->update($resourceModel->getMainTable(), $data, $where);
        }
    }
}
