<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magedelight\Megamenu\Api\Data;

interface CacheInterface
{

    const STORE_ID = 'store_id';
    const NAME = 'name';
    const CACHE_ID = 'cache_id';
    const HTML_VALUE = 'html_value';

    /**
     * Get cache_id
     * @return string|null
     */
    public function getCacheId();

    /**
     * Set cache_id
     * @param string $cacheId
     * @return \Magedelight\Megamenu\Cache\Api\Data\CacheInterface
     */
    public function setCacheId($cacheId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Magedelight\Megamenu\Cache\Api\Data\CacheInterface
     */
    public function setName($name);

    /**
     * Get store_id
     * @return string|null
     */
    public function getStoreId();

    /**
     * Set store_id
     * @param string $storeId
     * @return \Magedelight\Megamenu\Cache\Api\Data\CacheInterface
     */
    public function setStoreId($storeId);

    /**
     * Get html_value
     * @return string|null
     */
    public function getHtmlValue();

    /**
     * Set html_value
     * @param string $htmlValue
     * @return \Magedelight\Megamenu\Cache\Api\Data\CacheInterface
     */
    public function setHtmlValue($htmlValue);
}
