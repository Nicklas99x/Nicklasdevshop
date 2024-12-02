<?php

namespace Nicklas\SalesWatcher\Console\Command;

use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Nicklas\SalesWatcher\Model\OrderMonitor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;

class MonitorOrdersCommand extends Command
{
    /**
     * @var State
     */
    private $state;

    /**
     * @var OrderMonitor
     */
    private $orderMonitor;

    /**
     * Constructor.
     *
     * @param State $state
     * @param OrderMonitor $orderMonitor
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        \Nicklas\SalesWatcher\Model\OrderMonitor $orderMonitor
    ) {
        $this->state = $state;
        $this->orderMonitor = $orderMonitor;
        parent::__construct();
    }

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('saleswatcher:monitor')
            ->setDescription('Monitor failed orders and send email alerts');
        parent::configure();
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->setAreaCode('adminhtml');

            $output->writeln('<info>Monitoring failed orders...</info>');

            // Execute the order monitoring logic
            $this->orderMonitor->process();

            $output->writeln('<info>Order monitoring completed successfully.</info>');
            return Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>Unexpected error: ' . $e->getMessage() . '</error>');
            return Cli::RETURN_FAILURE;
        }
    }
}
