<?php

namespace Sebihojda\Mbp\Command;

/*use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:read-env',
    description: 'Add a short description for your command',
)]
class ReadEnvCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}*/

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:read-env',
    description: 'Reads and displays an environment variable.',
)]
class ReadEnvCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('variable_name', InputArgument::REQUIRED, 'The name of the environment variable.')
            ->addOption('json', null, InputOption::VALUE_NONE, 'Output in JSON format.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $variableName = $input->getArgument('variable_name');
        $isJson = $input->getOption('json');

        // Citim variabila de mediu
        $value = $_ENV[$variableName] ?? null;

        if ($value === null) {
            $io->error("Environment variable '{$variableName}' not found.");
            return Command::FAILURE;
        }

        if ($isJson) {
            $output->writeln(json_encode([$variableName => $value]));
        } else {
            $io->title("Value for '{$variableName}'");
            $io->text($value);
            $io->success('Done.');
        }

        return Command::SUCCESS;
    }
}
