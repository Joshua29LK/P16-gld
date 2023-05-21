<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Mass Order Actions for Magento 2
 */

namespace Amasty\Oaction\Model;

use Magento\Framework\App\ResourceConnection;

class Command
{
    /**
     * @var string
     */
    protected $_type = '';

    /**
     * @var array
     */
    protected $_info = [];

    /**
     * @var array
     */
    protected $_errors = [];

    //phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    public function __construct()
    {
    }

    public function getCreationData()
    {
        if (isset($this->_info)) {
            return $this->_info;
        } else {
            return false;
        }
    }

    /**
     * Gets list of not critical errors after the command execution
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    public function hasResponse()
    {
        return false;
    }

    public function getResponseName()
    {
        return '';
    }

    public function getResponseType()
    {
        return 'application/pdf';
    }

    public function getResponseBody()
    {
        return 'application/pdf';
    }
}
