<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

namespace Amasty\Flags\Plugin\View\Element\UiComponent\DataProvider;

use Magento\Framework\Api\Filter;

class DataProvider
{
    public function beforeAddFilter(
        \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider $subject,
        Filter $filter
    ) {
        if ($subject->getName() == 'sales_order_grid_data_source') {
            if (0 === strpos($filter->getField(), 'amflags_column_')) {
                $filter->setField($filter->getField() . '.flag_id');
            }
        }
    }
}
