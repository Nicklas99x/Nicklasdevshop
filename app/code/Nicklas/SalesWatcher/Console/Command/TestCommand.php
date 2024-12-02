<?php
namespace Nicklas\SalesWatcher\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected function configure()
    {
        $this->setName('saleswatcher:test')
            ->setDescription('Test command')
            ->setHelp('This command tests the SalesWatcher functionality.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Test command executed successfully!');
        return Command::SUCCESS;
    }
}
