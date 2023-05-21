<?php
/**
 * @author CynoInfotech Team
 * @package Cynoinfotech_IndexFollow
 */
namespace Cynoinfotech\IndexFollow\Setup;
  
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config;
  
class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;
  
    public function __construct(EavSetupFactory $eavSetupFactory, Config $eavConfig)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }
  
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
		$setup->startSetup();
		 
        $eavSetup = $this->eavSetupFactory->create(array('setup' => $setup));
        
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'indexfollow_enable',
            array(
                'group' => 'Index Follow',
                'type' => 'text',
                'backend' => '',   
                'frontend' => '',
                'label' => 'Enable Index Follow',
                'input' => 'select',
                'class' => 'indexfollow_enable',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,                
            )    
        );         
        
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'indexfollow_followvalue',
            array(
                'group' => 'Index Follow',
                'type' => 'text',
                'backend' => '',   
                'frontend' => '',
                'label' => 'Follow Value',
                'input' => 'select',
                'class' => 'indexfollow_followvalue',
                'source' => 'Cynoinfotech\IndexFollow\Model\Config\Source\FollowOptions',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'option' => array( 
                    'values' => array(),
                )
            )    
        ); 
        
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'indexfollow_indexvalue',
            array(
                'group' => 'Index Follow',
                'type' => 'text',
                'backend' => '',   
                'frontend' => '',
                'label' => 'Index Value',
                'input' => 'select',
                'class' => 'indexfollow_indexvalue',
                'source' => 'Cynoinfotech\IndexFollow\Model\Config\Source\IndexOptions',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'option' => array( 
                    'values' => array(),
                )
            )    
        );
		
		
		     
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'indexfollow_enable',
			array(
				'type'         => 'text',
				'label'        => 'Enable Index Follow',
				'input'        => 'select',
				'sort_order'   => 100,
				'source'       => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
				'global'        => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'visible'      => true,
				'required'     => false,
				'user_defined' => false,
				'default'      => null,
				'group'        => 'Index Follow',
				'backend'      => ''        
				
				)
		);        
       
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'indexfollow_followvalue',
			array(
				'type'         => 'text',
				'label'        => 'Follow Value',
				'input'        => 'select',
				'sort_order'   => 101,
				'source'        => 'Cynoinfotech\IndexFollow\Model\Config\Source\FollowOptions',
				'global'        => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'visible'      => true,
				'required'     => false,
				'user_defined' => false,
				'default'      => null,
				'group'        => 'Index Follow',
				'backend'      => ''                        
			)
		);
                
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'indexfollow_indexvalue',
			array(
				'type'         => 'text',
				'label'        => 'Index Value',
				'input'        => 'select',
				'sort_order'   => 102,
				'source'       => 'Cynoinfotech\IndexFollow\Model\Config\Source\IndexOptions',
				'global'        => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'visible'      => true,
				'required'     => false,
				'user_defined' => false,
				'default'      => null,
				'group'        => 'Index Follow',
				'backend'      => ''                         
			)
		);
               
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'indexfollow_associatedproducts',
			array(
				'type'         => 'text',
				'label'        => 'Enable For Associated Products',
				'input'        => 'select',
				'sort_order'   => 103,
				'source'       => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
				'global'        => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'visible'      => true,
				'required'     => false,
				'user_defined' => false,
				'default'      => 0,
				'group'        => 'Index Follow',
				'backend'      => ''                        
			)
		); 

		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'indexfollow_priority',
			array(
				'type'         => 'int',
				'label'        => 'Priority',
				'input'        => 'text',
				'sort_order'   => 104,
				'source'       => '',
				'global'        => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'visible'      => true,
				'required'     => false,
				'user_defined' => false,
				'default'      => null,
				'group'        => 'Index Follow',
				'backend'      => '' 
			)
		); 
		
		$setup->getConnection()->addColumn(
                $setup->getTable('cms_page'),
                'indexfollow_enable',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => true,
                    'comment' => 'Index Follow Enable'
                )
            );       
       
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_page'),
                'indexfollow_followvalue',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => true,
                    'comment' => 'Follow Value'
                )
            );
            
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_page'),
                'indexfollow_indexvalue',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => true,
                    'comment' => 'Index Value'
                )
            );
		
		
		 $setup->endSetup();

        
    }
}