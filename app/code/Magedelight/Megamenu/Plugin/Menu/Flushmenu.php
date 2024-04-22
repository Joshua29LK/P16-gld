<?php
namespace Magedelight\Megamenu\Plugin\Menu;

use Magedelight\Megamenu\Controller\Adminhtml\Menu\Save;
use Magedelight\Megamenu\Helper\Cache;

/**
 * Class Flushmenu
 * @package Magedelight\Megamenu\Plugin\Menu
 */
class Flushmenu
{
    /**
     * @var Cache
     */
    protected $helperData;

    /**
     * @param Cache $helperData
     */
    public function __construct(
        Cache $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @param Save $subject
     * @param $result
     * @return mixed
     * @throws \Exception
     */
    public function afterExecute(Save $subject, $result)
    {
        if ($this->helperData->enableCustomMenu()) {
            $this->helperData->updateVariableByCode($this->helperData->getStoreMenuKey());
        }
        return $result;
    }
}
