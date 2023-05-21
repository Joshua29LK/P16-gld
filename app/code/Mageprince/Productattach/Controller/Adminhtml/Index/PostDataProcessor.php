<?php

/**
 * MagePrince
 * Copyright (C) 2020 Mageprince <info@mageprince.com>
 *
 * @package Mageprince_Productattach
 * @copyright Copyright (c) 2020 Mageprince (http://www.mageprince.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MagePrince <info@mageprince.com>
 */

namespace Mageprince\Productattach\Controller\Adminhtml\Index;

class PostDataProcessor
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    private $dateFilter;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * PostDataProcessor constructor.
     *
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->dateFilter = $dateFilter;
        $this->messageManager = $messageManager;
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array $data
     * @return array
     */
    public function filter($data)
    {
        return $data;
    }

    /**
     * Validate post data
     *
     * @return bool Return FALSE if someone item is invalid
     */
    public function validate()
    {
        $errorNo = true;
        return $errorNo;
    }
}
