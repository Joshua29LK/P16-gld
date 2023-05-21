<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace FME\CanonicalUrl\Model\Config\Source;

/**
 * Class IsActive
 */
class ListCategories   extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var \FME\SeoMetaTagsGenerator\Model\Metatemplate
     */
    protected $_categoryHelper;
    protected $categoryFlatConfig;
    protected $categoryRepository;
    protected $_categoryPath;


    /**
     * Constructor
     *
     * @param \FME\SeoMetaTagsGenerator\Model\Metatemplate $_seometatagsgeneratorgroupRule
     */
    public function __construct( \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        \Magento\Catalog\Model\CategoryFactory $categoryRepository,
        \Magento\Catalog\Model\ResourceModel\Category $categoryPath)
    {
        $this->_categoryHelper = $categoryHelper;
        $this->categoryFlatConfig = $categoryFlatState;
        $this->categoryRepository = $categoryRepository;
        $this->_categoryPath = $categoryPath;

    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {   


        $options = [];
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();        

        $categoryCollection = $objectManager->get('\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
        $categories = $categoryCollection->create();
        $categories->addAttributeToSelect('*');
 
        $options[] = [
                'label' => 'Use Config',
                'value' => 'config',
            ];

        foreach ($categories as $category) {

            $fullpath = $this->_categoryPath->getCategoryPathById($category->getId());
            $ids = explode("/", $fullpath);
            $opt = '';
            foreach ($ids as $id){
                $category = $this->categoryRepository->create()->load($id);
                $categoryName = $category->getName();
                $opt = $opt.$categoryName.' -> ';  
            }

            if($opt){
                $opt = substr($opt, 0, -3);
            }      
            $options[] = [
                'label' => $opt,
                'value' => $category->getId(),
                'data-title' => $opt,
            ];
        }


        return $options;
    }

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }

}
