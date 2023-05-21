<?php
namespace RedChamps\SitemapUrlExclude\Rewrite\Model;

class Sitemap extends \Magento\Sitemap\Model\Sitemap
{
    /*
     * Modified function to dispatch custom event +
     * excluded items which are not allowed
     * */
    protected function _getSitemapRow($url, $lastmod = null, $changefreq = null, $priority = null, $images = null)
    {
        //RedChamps: dispatched custom event to handle excluded pages
        $item = new \Magento\Framework\DataObject();
        $item->setUrl($url);
        $item->setIsAllowed(true);
        $this->_eventManager->dispatch('sitemap_item_process_before', ['item' => $item, 'sitemap' => $this]);
        //RedChamps: skipped item if not allowed starts
        if(!$item->getIsAllowed()) {
            return "";
        }
        //RedChamps: skipped item if not allowed ends
        return parent::_getSitemapRow($url, $lastmod, $changefreq, $priority, $images);
    }
}