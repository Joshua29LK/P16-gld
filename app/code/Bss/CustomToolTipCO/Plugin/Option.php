<?php
namespace Bss\CustomToolTipCO\Plugin;

class Option
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Bss\CustomToolTipCO\Model\ToolTipCOFactory $toolTipCOFactory
    ) {
        $this->logger = $logger;
        $this->toolTipCOFactory = $toolTipCOFactory;
    }


    public function afterSave($subject, $result) 
    {
        $toolTipModel = $this->toolTipCOFactory->create()->loadByOptionId($subject->getOptionId());
        if (!$toolTipModel) {
            $toolTipModel = $this->toolTipCOFactory->create();
        }

        $content = $subject->getData('tooltip_content');
        
        $toolTipModel->setOptionId($subject->getOptionId())->setContent($content)->save();

        return $result;
    }

    public function afterGetData($subject, $result, $key = '', $index = null) 
    {
        if ($key === '') {
            if (isset($result['option_id'])) {
                $toolTip = $this->toolTipCOFactory->create()->loadByOptionId($result['option_id']);
                $result['tooltip_content'] = $toolTip ? $toolTip->getData('content') : null;
            } else {
                $result['tooltip_content'] = null;
            }
        }

        if (($key === 'tooltip_content') && !$subject->hasData('tooltip_content')) {
            $toolTip = $this->toolTipCOFactory->create()->loadByOptionId($subject->getData('option_id'));
            return $toolTip ? $toolTip->getData('content') : null;
        }
        return $result;
    }
}
