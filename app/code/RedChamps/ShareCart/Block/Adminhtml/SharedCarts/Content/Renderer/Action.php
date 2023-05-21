<?php
namespace RedChamps\ShareCart\Block\Adminhtml\SharedCarts\Content\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class Action extends AbstractRenderer
{
    public function render(DataObject $row)
    {
        $url = $this->getUrl('*/*/redirect', ['unique_id'=>$row->getUniqueId(), 'store_id' => $row->getStoreId()]);
        return sprintf("<a target='_blank' href='%s'>%s</a>", $url, __('View Cart'));
    }
}
