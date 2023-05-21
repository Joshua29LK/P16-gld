<?php
namespace RedChamps\SitemapUrlExclude\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SitemapItemBefore implements ObserverInterface
{
    const XML_PATH_EXCLUDE_PAGE = "sitemap/exclude/links";

    const XML_PATH_EXCLUDE_PATTERN = "sitemap/exclude/patterns";

    protected $_excludedPatterns;

    protected $scopeConfig;

    protected $excludedPages;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(Observer $observer)
    {
        $item = $observer->getEvent()->getItem();
        $sitemap = $observer->getEvent()->getSitemap();
        $storeId = $sitemap->getStoreId();
        $item->setIsAllowed(true);
        $excludedPages = [];
        if ($this->getExcludedPages($storeId)) {
            $excludedPages = preg_split('/\r\n|[\r\n]/', $this->getExcludedPages($storeId));
        }
        if (($excludedPages && in_array($item->getUrl(), $excludedPages)) || $this->matchRules($item->getUrl(), $storeId)) {
            $item->setIsAllowed(false);
        }
    }

    public function getExcludedPages($storeId)
    {
        if (!$this->excludedPages) {
            $this->excludedPages = $this->getConfig(self::XML_PATH_EXCLUDE_PAGE, $storeId);
        }
        return $this->excludedPages;
    }

    protected function getExcludedPatterns($storeId)
    {
        if (!$this->_excludedPatterns) {
            $this->_excludedPatterns = $this->getConfig(self::XML_PATH_EXCLUDE_PATTERN, $storeId);
            if ($this->_excludedPatterns) {
                $this->_excludedPatterns = preg_split('/\r\n|[\r\n]/', $this->_excludedPatterns);
            }
        }
        return $this->_excludedPatterns;
    }

    public function matchRules($stringVal, $storeId = false, $caseSensitiveVal = false)
    {
        $rules = $this->getExcludedPatterns($storeId);
        $stringVal = $this->removeSlashes($stringVal);

        if (!is_array($rules)) {
            return false;
        }
        foreach ($rules as $patternVal) {
            if ($this->checkPattern($stringVal, $patternVal, $caseSensitiveVal)) {
                return true;
            }
        }

        return false;
    }

    public function removeSlashes($url)
    {
        if ($url != "/") {
            $url = ltrim($url, '/');
            $url = rtrim($url, '/');
        }
        return$url;
    }

    public function cleanHostUrl($urlWithHost)
    {
        $parts = parse_url($urlWithHost);
        $url = $urlWithHost;
        if (isset($parts['path'])) {
            $url = $parts['path'];
        }
        $url = str_replace('index.php/', '', $url);
        $url = str_replace('index.php', '', $url);
        if (isset($parts['query'])) {
            $url.= '?' . $parts['query'];
        }
        return $url;
    }

    public function checkPattern($url, $pattern, $caseSensitive = false)
    {
        $string = $this->cleanHostUrl($url);
        $pattern = $this->removeSlashes($this->cleanHostUrl($pattern));

        if (!$caseSensitive) {
            $string  = strtolower($string);
            $pattern = strtolower($pattern);
        }

        $parts = explode('*', $pattern);
        $index = 0;

        $shouldBeFirst = true;
        $shouldBeLast  = true;

        foreach ($parts as $part) {
            if ($part == '') {
                $shouldBeFirst = false;
                continue;
            }

            $index = strpos($string, $part, $index);

            if ($index === false) {
                return false;
            }

            if ($shouldBeFirst && $index > 0) {
                return false;
            }

            $shouldBeFirst = false;
            $index += strlen($part);
        }

        if (count($parts) == 1) {
            return ($string == $pattern);
        }

        $last = end($parts);
        if ($last == '') {
            return $pattern;
        }

        if (strrpos($string, $last) === false) {
            return false;
        }

        if (strlen($string) - strlen($last) - strrpos($string, $last) > 0) {
            return false;
        }

        return true;
    }

    protected function getConfig($path, $storeId)
    {
        return $this->scopeConfig->getValue($path, 'store', $storeId);
    }
}
