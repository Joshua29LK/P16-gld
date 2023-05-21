<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Logger;

interface GeneralLoggerInterface
{

    /**
     * @param $type
     * @param $data
     * @return void
     */
    public function add($type, $data);
}
