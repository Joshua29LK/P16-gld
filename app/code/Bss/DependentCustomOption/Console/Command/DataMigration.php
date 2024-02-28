<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_DependentCustomOption
 * @author     Extension Team
 * @copyright  Copyright (c) 2020-2022 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
declare(strict_types=1);

namespace Bss\DependentCustomOption\Console\Command;

use Bss\DependentCustomOption\Model\ResourceModel\Dco;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DataMigration extends Command
{
    private const LIMIT = 'limit';

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $manager;

    /**
     * @var Dco
     */
    protected $dco;

    /**
     * Construct.
     *
     * @param \Magento\Framework\Module\Manager $manager
     * @param Dco $dco
     */
    public function __construct(
        \Magento\Framework\Module\Manager $manager,
        Dco $dco
    ) {
        parent::__construct();
        $this->manager = $manager;
        $this->dco = $dco;
    }

    /**
     * @inheritDoc
     */
    public function configure()
    {
        $this->setName('dco:migration');
        $this->setDescription(
            'This is a command which migrate old dependent custom option data (v1.0.4)
            to new dependent custom option data(newest version) (Bss_DependentCustomOption).
            Please back up table bss_depend_co before do this action.'
        );
        $this->addOption(
            self::LIMIT,
            null,
            InputOption::VALUE_OPTIONAL,
            'Limit Product'
        );

        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->manager->isEnabled('Bss_DependentCustomOption')) {
            $output->writeln('<comment>' . 'Module `Bss_DependentCustomOption` is disabled. Skip this action.' . '</comment>');
        } else {
            $limit = $input->getOption(self::LIMIT);
            if (!$limit || (int)$limit <= 0) {
                $limit = 0;
                $output->writeln(
                    '<comment>' . 'Limit product params not be set(fix bug data in v1.0.4). The migration action is processing all data in `bss_depend_co` table.' . '</comment>'
                );
            }
            $output->writeln('<info>' . 'Migrate Action Start.' . '</info>');

            $rows = $this->dco->migrate($limit);

            $output->writeln(
                '<info>' . sprintf('Done: %d row(s).', (int)$rows['success']) . '</info>'
            );
            if (!empty($rows['error'])) {
                $output->writeln(
                    '<comment>' . sprintf('Fail migrate data in list product id(s): %s.', trim($rows['error'], ', ')) . '</comment>'
                );
            }
        }
    }
}
