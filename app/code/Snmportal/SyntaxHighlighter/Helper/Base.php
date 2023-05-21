<?php
namespace Snmportal\SyntaxHighlighter\Helper;

use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Base
 * @package Snmportal\SyntaxHighlighter\Helper
 */
class Base extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MNAME = 'Snmportal_SyntaxHighlighter';
    const MKEY = 'snm-sh-m2-001';
    const MLOG = 'snm-portal_sh.log';
    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $logDirectory;
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $tmpDirectory;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $_backendUrl;


    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    private $resourceConfig;

    /**
     * @var string
     */
//    private $locale;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var \Magento\Framework\Module\ModuleResource
     */
    private $moduleResource;

//    private $fileCounter = 0;
    //  private $fileprev = '';

    /**
     * @var \Magento\Config\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\App\Cache\Type\Collection
     */
    private $cache;

    /**
     * Base constructor.
     * @param \Magento\Config\Model\Config $configModel
     * @param \Magento\Framework\App\Cache\Type\Collection $cache
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Module\ModuleResource $moduleResource
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Config\Model\ResourceModel\Config $resourceConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     */
    public function __construct(
        \Magento\Config\Model\Config $configModel,
        \Magento\Framework\App\Cache\Type\Collection $cache,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Module\ModuleResource $moduleResource,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
        //,
        //\Magento\Backend\Model\Session $session

    )
    {
        $this->cache = $cache;
        $this->config = $configModel;
        $this->moduleResource = $moduleResource;
        $this->productMetadata = $productMetadata;
        $this->storeManager = $storeManager;
        $this->_backendUrl = $backendUrl;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->tmpDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::TMP);
        $this->logDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::LOG);

        $this->resourceConfig = $resourceConfig;
        parent::__construct(
            $context
        );
    }

    /**
     * @param $message
     * @param null $obj
     */
    public static function notice($message, $obj = null)
    {
        \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Snmportal\SyntaxHighlighter\Helper\Base')
            ->Message(200, $message, $obj);
    }

    /**
     * @param $message
     * @param null $obj
     */
    public static function debug($message, $obj = null)
    {
        \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Snmportal\SyntaxHighlighter\Helper\Base')
            ->Message(100, $message, $obj);
    }

    /**
     * @param $message
     * @param null $obj
     */
    public static function snm($message, $obj = null)
    {
        \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Snmportal\SyntaxHighlighter\Helper\Base')
            ->MessageSnm($message, $obj);
    }

    /**
     * @param $level
     * @param $message
     * @param $object
     */
    public function Message(
        $level,
        $message,
        /** @noinspection PhpUnusedParameterInspection */
        $object
    ) {

        if ($level >= 400 || $this->islogEnabled()) {
            if ($message instanceof \Exception) {
                $message = $message->getMessage();
            }
            $context = [];
            if ($level == 400) {
                $context['exception'] = $message;
                $message .= "\n" . $this->backtrace(true);
            }
            \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Psr\Log\LoggerInterface')
                ->log($level, $message, $context);
        }
    }

    /**
     * @return bool
     */
    public function islogEnabled()
    {
        return $this->getStoreConfig('snmportal_syntaxhighlighter/general/logging') > 0;
    }

    /**
     * @param $pfad
     * @param null $store
     * @return mixed
     */
    public function getStoreConfig($pfad, $store = null)
    {
        return $this->scopeConfig->getValue(
            $pfad, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store
        );
    }

    /**
     * @param bool $bfull
     * @return mixed|string
     */
    private function backtrace($bfull = false)
    {
        $e = new \Exception();
        $trace = explode("\n", $e->getTraceAsString());
        array_shift($trace);
        array_shift($trace);
        if ($bfull) {
            return implode("\n", $trace);
        }
        $x = array_shift($trace);
        return $x;
    }

    /**
     * @param $message
     * @param $object
     */
    public function MessageSnm(
        $message,
        /** @noinspection PhpUnusedParameterInspection */
        $object
    ) {
        if ($this->islogEnabled()) {
            if ($message instanceof \Exception) {
                $message = $message->getMessage();
            }
            $message = "\n[" . date('Y-m-d H:m:s', time()) . '] ' . $message;
            $message .= "\n" . $this->backtrace();
            $this->logDirectory->writeFile(self::MLOG, $message, 'a+');
        }
    }

    public function __destruct()
    {
    }

    /**
     * @return \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    public function getMediaDirectory()
    {
        return $this->mediaDirectory;
    }

    /**
     * @param $file
     * @return string
     */
    public function getMediaUrl($file)
    {
        return $this->getBaseMediaUrl() . $file;
    }

    /**
     * @return mixed
     */
    public function getBaseMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @return bool|false|string
     */
    public function getModulVersion()
    {
        return $this->moduleResource->getDataVersion(self::MNAME);
    }

    /**
     * @return array|bool|mixed|string
     */
    public function Info()
    {
        $result = false;
        try {
            $cacheKey = 'snminfo_' . self::MNAME;
            $data = $this->cache->load($cacheKey);
            if ($data) {
                $data = unserialize($data);
            }
            $params = $this->getParam();
            if (is_array($data)) {
                $result = $data;
                if (!isset($result['s']) || $result['s'] != $params['s']) {
                    $result = false;
                } else {
                    if (!isset($result['ek']) || $result['ek'] != $params['ek']) {
                        $result = false;
                    }
                }
            }
            if (!$result) {
                $params = base64_encode(\Zend\Json\Json::encode($params));
                $url = 'htt' . 'ps:/' . '/sn' . 'm-por' . 'tal.c' . 'om/me' . 'dia/mo' . 'dule/mo' . 'dule.json';
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $jsoninfo = file_get_contents($url . '?i=' . $params);
                } else {
                    $client = new \Zend\Http\Client($url, ['timeout' => 10]);
                    $client->setParameterGet(['i' => $params]);
                    $jsoninfo = $client->send()->getBody();
                }
                if ($jsoninfo) {
                    $info = json_decode($jsoninfo, true);
                    if (is_array($info) && isset($info['version'])) {
                        $result = $info;
                        $this->cache->save(serialize($result), $cacheKey);
                    }
                }
            }
        } catch (\Exception $e) {
            self::exception($e->getMessage());
        }
        return $result;
    }

    /**
     * @return array
     */
    private function getParam()
    {
        return [
            's' => trim($this->getServerName()),
            'mv' => $this->productMetadata->getVersion(),
            'ex' => self::MKEY,
            'ek' => trim($this->getStoreConfig('snmportal_publ' . 'ication' . '/gen' . 'eral/li' . 'ce' . 'nse')),
            'ev' => $this->moduleResource->getDataVersion(self::MNAME)
        ];
    }
    public function getRemoteResourceUrl($mode)
    {
        $param = base64_encode(\Zend\Json\Json::encode($this->getParam()));
        $url = 'htt' . 'ps:/' . '/sn' . 'm-por' . 'tal.c' . 'om/me' . 'dia/mo' . 'dule/mo' . 'dule.json';

        return 'htt' . 'ps:/' . '/sn' . 'm-por' . 'tal.c' . 'om/me' . 'dia/mo' . 'dule/'.$mode.'/'.$param.'';
        return '//192.168.0.12/magento2/snm/pub/me' . 'dia/mo' . 'dule/'.$mode.'/'.$param.'';

    }
    /**
     * @return mixed|string
     */
    public function getServerName()
    {
        $url = $this->_backendUrl->getUrl();
        $parsedUrl = parse_url($url, PHP_URL_HOST);
        if ($parsedUrl !== null) {
            return $parsedUrl;
        }
        return $url;
    }

    /**
     * @param $message
     * @param null $obj
     */
    public static function exception($message, $obj = null)
    {
        \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Snmportal\SyntaxHighlighter\Helper\Base')
            ->Message(400, $message, $obj);
    }


}
