<?php
namespace RedChamps\WeightDisplay\Model;

class TotalWeightCalculator
{
    protected $configReader;

    public function __construct(ConfigReader $configReader)
    {
        $this->configReader = $configReader;
    }

    public function execute($object, $type = "quote")
    {
        $weight = 0;
        foreach ($object->getAllVisibleItems() as $item) {
            $qty = $item->getQty();
            if ($type == "order") {
                $qty = $item->getQtyOrdered()-(float)$item->getQtyRefunded();
            }
            $weight+= $item->getWeight()*$qty;
        }
        return $weight ? $this->configReader->getFormattedWeight($weight) : false;
    }
}
