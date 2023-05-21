<?php
namespace Bss\CustomBalticodeCategoryConfigurator\Plugin;

class GlassShapeService
{
    /**
     * @param \Balticode\CategoryConfigurator\Model\GlassShapeService $subject
     * @param $result
     * @param $glassShapes
     * @return mixed
     */
    public function afterCalculateQty(
        \Balticode\CategoryConfigurator\Model\GlassShapeService $subject,
        $result,
        $glassShapes
    ){
        if (!isset($glassShapes['custom_bss'])) {
            return 1;
        }

        return $result;
    }
}
