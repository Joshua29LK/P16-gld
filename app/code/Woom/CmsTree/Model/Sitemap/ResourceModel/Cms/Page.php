<?php

namespace Woom\CmsTree\Model\Sitemap\ResourceModel\Cms;

use Magento\Sitemap\Model\ResourceModel\Cms\Page as ParentPage;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\Exception\LocalizedException;

class Page extends ParentPage
{
    /**
     * Retrieve cms page collection array
     *
     * @param int $storeId
     * @return array
     * @throws LocalizedException
     * @throws \Zend_Db_Statement_Exception
     */
    public function getCollection($storeId)
    {
        $entityMetadata = $this->metadataPool->getMetadata(PageInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $this->getConnection()->select()->from(
            ['main_table' => $this->getMainTable()],
            [$this->getIdFieldName(), 'url' => 'identifier', 'updated_at' => 'update_time']
        )->join(
            ['store_table' => $this->getTable('cms_page_store')],
            "main_table.{$linkField} = store_table.$linkField",
            []
        )->joinLeft(
            ['cms_tree_table' => $this->getTable('woom_cms_page_tree')],
            "main_table.{$linkField} = cms_tree_table.{$linkField}",
            ['tree_url' => 'request_url']
        )->where(
            'main_table.is_active = 1'
        )->where(
            'main_table.identifier != ?',
            \Magento\Cms\Model\Page::NOROUTE_PAGE_ID
        )->where(
            'store_table.store_id IN(?)',
            [0, $storeId]
        );

        $pages = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $page = $this->_prepareObject($row);
            $pages[$page->getId()] = $page;
        }

        return $pages;
    }

    /**
     * Prepare page object
     *
     * @param array $data
     * @return \Magento\Framework\DataObject
     */
    protected function _prepareObject(array $data)
    {
        $object = parent::_prepareObject($data);

        if (isset($data['tree_url']) && !empty($data['tree_url'])) {
            $object->setUrl($data['tree_url']);
        }

        return $object;
    }
}