<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Order Notes for Magento 2
*/

namespace Amasty\Flags\Ui\Component;

use Amasty\Flags\Model\Column;
use Amasty\Flags\Model\Flag;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\AbstractComponent;

class FlagAssign extends AbstractComponent
{
    /**
     * @var \Amasty\Flags\Model\ResourceModel\Column\CollectionFactory
     */
    private $columnCollectionFactory;
    /**
     * @var \Amasty\Flags\Model\ResourceModel\Flag\CollectionFactory
     */
    private $flagCollectionFactory;
    /**
     * @var UrlInterface
     */
    private $url;
    /**
     * @var Flag
     */
    private $flagSingleton;

    public function __construct(
        ContextInterface $context,
        \Amasty\Flags\Model\ResourceModel\Flag\CollectionFactory $flagCollectionFactory,
        \Amasty\Flags\Model\ResourceModel\Column\CollectionFactory $columnCollectionFactory,
        UrlInterface $url,
        Flag $flagSingleton,
        $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->columnCollectionFactory = $columnCollectionFactory;
        $this->flagCollectionFactory = $flagCollectionFactory;
        $this->url = $url;
        $this->flagSingleton = $flagSingleton;
    }

    /**
     * Get component name
     *
     * @return string
     */
    public function getComponentName()
    {
        return 'flag_assign';
    }

    public function prepare()
    {
        /** @var \Amasty\Flags\Model\ResourceModel\Column\Collection $columnCollection */
        $columnCollection = $this->columnCollectionFactory->create();

        $columns = [];
        /** @var Column $column */
        foreach ($columnCollection as $column) {
            $columns[$column->getId()] = [
                'id' => (int)$column->getId(),
                'title' => $column->getName(),
                'comment' => $column->getComment(),
                'flagIds' => array_map(
                    function ($id) {
                        return (int)$id;
                    },
                    $column->getAppliedFlagIds()
                )
            ];
        }

        /** @var \Amasty\Flags\Model\ResourceModel\Flag\Collection $flagCollection */
        $flagCollection = $this->flagCollectionFactory->create();

        $flags = [];
        /** @var Flag $flag */
        foreach ($flagCollection as $flag) {
            $flags[$flag->getId()] = [
                'id' => (int)$flag->getId(),
                'title' => $flag->getName(),
                'image_src' => $flag->getImageUrl(),
                'defaultNote' => $flag->getNote()
            ];
        }

        $config = $this->getData('config');

        $config['columns'] = $columns;
        $config['flags'] = $flags;
        $config['actionUrl'] = $this->url->getUrl('amasty_flags/flagAssign/assign');
        $config['imagePlaceholder'] = $this->flagSingleton->getImagePlaceholderUrl();

        $this->setData('config', $config);

        parent::prepare();
    }
}
