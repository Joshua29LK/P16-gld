<?php

namespace Balticode\CategoryConfigurator\Setup;

use Balticode\CategoryConfigurator\Model\Attribute\Source\GlassShape;
use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory;
use Magento\Catalog\Api\ProductCustomOptionRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as EavAttributeModel;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend;
use Magento\Eav\Model\Entity\Attribute\Backend\DefaultBackend;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\App\State;

class InstallData implements InstallDataInterface
{
    const GLASS_SHAPES = 'glass_shapes';
    const GLASS_TYPE = 'glass_type';

    const OPTION_TITLE_HEIGHT = 'Height';
    const OPTION_TITLE_WIDTH = 'Width';
    const OPTION_TITLE_TOP_WIDTH = 'Top Width';
    const OPTION_TITLE_BOTTOM_WIDTH = 'Bottom Width';

    const ATTRIBUTE_SET_NAME_CONFIGURATOR_PRODUCTS = 'Configurator Products';

    /**
     * @var AttributeSetFactory
     */
    protected $attributeSetFactory;

    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var Attribute
     */
    protected $eavAttribute;

    /**
     * @var ProductCustomOptionRepositoryInterface
     */
    protected $productCustomOptionRepository;

    /**
     * @var ProductCustomOptionInterfaceFactory
     */
    protected $customOptionInterfaceFactory;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var State
     */
    protected $state;

    /**
     * @var AttributeSetRepositoryInterface
     */
    protected $attributeSetRepository;

    /**
     * @param AttributeSetFactory $attributeSetFactory
     * @param EavSetupFactory $eavSetupFactory
     * @param Attribute $eavAttribute
     * @param ProductCustomOptionRepositoryInterface $productCustomOptionRepository
     * @param ProductCustomOptionInterfaceFactory $customOptionInterfaceFactory
     * @param ProductFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param State $state
     * @param AttributeSetRepositoryInterface $attributeSetRepository
     */
    public function __construct(
        AttributeSetFactory $attributeSetFactory,
        EavSetupFactory $eavSetupFactory,
        Attribute $eavAttribute,
        ProductCustomOptionRepositoryInterface $productCustomOptionRepository,
        ProductCustomOptionInterfaceFactory $customOptionInterfaceFactory,
        ProductFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        State $state,
        AttributeSetRepositoryInterface $attributeSetRepository
    ) {
        $this->attributeSetFactory = $attributeSetFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavAttribute = $eavAttribute;
        $this->productCustomOptionRepository = $productCustomOptionRepository;
        $this->customOptionInterfaceFactory = $customOptionInterfaceFactory;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->state = $state;
        $this->attributeSetRepository = $attributeSetRepository;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->addConfiguratorProductsAttributeSet($setup);
        $this->addGlassShapesAttribute($setup);
        $this->addGlassShapeTypeAttribute($setup);

        $this->state->setAreaCode('adminhtml');

        try {
            $this->productRepository->get('sample-glass-shape');
        } catch (NoSuchEntityException $e) {
            $sampleGlassShapeSku = $this->createSampleGlassShape($setup);
            $this->createGlassShapeOptions($sampleGlassShapeSku);
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function addConfiguratorProductsAttributeSet(ModuleDataSetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
        $defaultAttributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);

        /** @var \Magento\Eav\Model\Entity\Attribute\Set $configuratorProductsAttributeSet */
        $configuratorProductsAttributeSet = $this->attributeSetFactory->create();

        $data = [
            'attribute_set_name' => self::ATTRIBUTE_SET_NAME_CONFIGURATOR_PRODUCTS,
            'entity_type_id' => $entityTypeId,
        ];

        $configuratorProductsAttributeSet->setData($data);

        try {
            $configuratorProductsAttributeSet->validate();
        } catch (LocalizedException $exception) {
            return;
        }

        $this->attributeSetRepository->save($configuratorProductsAttributeSet);
        $configuratorProductsAttributeSet->initFromSkeleton($defaultAttributeSetId);
        $this->attributeSetRepository->save($configuratorProductsAttributeSet);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws LocalizedException
     */
    protected function addGlassShapesAttribute(ModuleDataSetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if ($eavSetup->getAttribute(Product::ENTITY, self::GLASS_SHAPES)) {
            return;
        }

        $eavSetup->addAttribute(Product::ENTITY, self::GLASS_SHAPES,
            [
                'type' => 'text',
                'label' => 'Glass Shapes',
                'input' => 'multiselect',
                'required' => false,
                'default' => '0',
                'global'   => ScopedAttributeInterface::SCOPE_STORE,
                'source' => GlassShape::class,
                'backend' => ArrayBackend::class,
                'group' => '',
                'visible' => true,
                'user_defined' => true,
                'system' => 0,
                'attribute_set' => self::ATTRIBUTE_SET_NAME_CONFIGURATOR_PRODUCTS
            ]
        );

        $configuratorProductsAttributeSetId = $this->getConfiguratorProductsAttributeSetId($setup);
        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
        $eavSetup->addAttributeToSet($entityTypeId, $configuratorProductsAttributeSetId, 'General', self::GLASS_SHAPES);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws LocalizedException
     */
    protected function addGlassShapeTypeAttribute(ModuleDataSetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if ($eavSetup->getAttribute(Product::ENTITY, self::GLASS_TYPE)) {
            return;
        }

        $eavSetup->addAttribute(Product::ENTITY, self::GLASS_TYPE,
            [
                'attribute_model' => EavAttributeModel::class,
                'backend' => DefaultBackend::class,
                'type' => 'int',
                'input' => 'select',
                'label' => 'Glass Type',
                'source' => Table::class,
                'required' => false,
                'user_defined' => true,
                'global'   => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'system' => 0,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'is_html_allowed_on_front' => true,
                'attribute_set' => self::ATTRIBUTE_SET_NAME_CONFIGURATOR_PRODUCTS
            ]
        );

        $configuratorProductsAttributeSetId = $this->getConfiguratorProductsAttributeSetId($setup);
        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
        $eavSetup->addAttributeToSet($entityTypeId, $configuratorProductsAttributeSetId, 'General', self::GLASS_TYPE);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return int|null
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws LocalizedException
     * @throws StateException
     */
    protected function createSampleGlassShape(ModuleDataSetupInterface $setup)
    {
        $configuratorProductsAttributeSetId = $this->getConfiguratorProductsAttributeSetId($setup);

        $sampleGlassShape = $this->productFactory->create();

        $sampleGlassShape->setTypeId('simple');
        $sampleGlassShape->setAttributeSetId($configuratorProductsAttributeSetId);
        $sampleGlassShape->setName('Sample Glass Shape');
        $sampleGlassShape->setSku('sample-glass-shape');
        $sampleGlassShape->setPrice(0);

        $sampleGlassShape = $this->productRepository->save($sampleGlassShape);

        return $sampleGlassShape->getSku();
    }

    /**
     * @param string $sampleGlassShapeSku
     */
    protected function createGlassShapeOptions($sampleGlassShapeSku)
    {
        if (!$sampleGlassShapeSku) {
            return;
        }

        $this->createGlassShapeOption($sampleGlassShapeSku, self::OPTION_TITLE_HEIGHT, 1, 1);
        $this->createGlassShapeOption($sampleGlassShapeSku, self::OPTION_TITLE_WIDTH, 1, 2);
        $this->createGlassShapeOption($sampleGlassShapeSku, self::OPTION_TITLE_TOP_WIDTH, 0, 3);
        $this->createGlassShapeOption($sampleGlassShapeSku, self::OPTION_TITLE_BOTTOM_WIDTH, 0, 4);
    }

    /**
     * @param string $sampleGlassShapeSku
     * @param string $title
     * @param int $required
     * @param int $sortOrder
     */
    protected function createGlassShapeOption($sampleGlassShapeSku, $title, $required, $sortOrder)
    {
        $option = $this->customOptionInterfaceFactory->create();

        $option->setType(ProductCustomOptionInterface::OPTION_TYPE_FIELD);
        $option->setIsRequire($required);
        $option->setPrice(0);
        $option->setPriceType('fixed');
        $option->setTitle($title);
        $option->setProductSku($sampleGlassShapeSku);
        $option->setSortOrder($sortOrder);

        $this->productCustomOptionRepository->save($option);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return int
     * @throws LocalizedException
     */
    protected function getConfiguratorProductsAttributeSetId(ModuleDataSetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);

        return $eavSetup->getAttributeSetId($entityTypeId, self::ATTRIBUTE_SET_NAME_CONFIGURATOR_PRODUCTS);
    }
}
