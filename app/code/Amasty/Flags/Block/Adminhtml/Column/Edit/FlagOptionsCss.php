<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Flags\Block\Adminhtml\Column\Edit;

use Amasty\Flags\Model\ResourceModel\Flag\CollectionFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\View\Element\Template;

class FlagOptionsCss extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_Flags::column/edit/flags_options_css.phtml';

    /**
     * @var CollectionFactory
     */
    private $flagCollectionFactory;

    public function __construct(
        Template\Context $context,
        CollectionFactory $flagCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->flagCollectionFactory = $flagCollectionFactory;
    }

    public function getOptionsCss(): string
    {
        $flags = $this->flagCollectionFactory->create();
        $flags->addOrder('priority', AbstractDb::SORT_ORDER_ASC);
        $optionsStyle = [
            '#column_apply_flag option {padding-left: 20px; background-repeat: no-repeat;}'
        ];

        foreach ($flags as $flag) {
            $optionsStyle[] = sprintf(
                '#column_apply_flag option[value="%s"] {'
                . 'background-image: url("%s");}',
                $flag->getId(),
                $flag->getImageUrl()
            );
        }

        return implode("\n", $optionsStyle);
    }
}
