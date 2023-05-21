<?php
namespace Medeka\Categorydescription\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;

class InstallData implements InstallDataInterface
{

	private $eavSetupFactory;

	public function __construct(EavSetupFactory $eavSetupFactory)
	{
		$this->eavSetupFactory = $eavSetupFactory;
	}

	public function install(
		ModuleDataSetupInterface $setup,
		ModuleContextInterface $context
	)
	{
		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'mk_catomschrijving',
			[
				'type'         => 'text',
				'label'        => 'Omschrijving boven',
				'input'        => 'textarea',
				'sort_order'   => 10,
				'global'       => 1,
				'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'wysiwyg_enabled' => true,
                'is_html_allowed_on_front' => true,
				'visible'      => true,
				'required'     => false,
				'group'        => ''
			]
		);
	}
}