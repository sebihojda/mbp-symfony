<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\Command;

use Sebihojda\Mbp\IO\CsvReader;
use Sebihojda\Mbp\IO\CsvWriter;
use Sebihojda\Mbp\TableProcessor\Config\TableInnerJoinerConfig;
use Sebihojda\Mbp\TableProcessor\Processor\TableInnerJoiner;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'app:csv:inner-join',
    description: 'Joins two CSV files based on a related column (INNER JOIN).',
)]
class CsvInnerJoinCommand extends Command
{
    public function __construct(
        private readonly TableInnerJoiner $tableInnerJoiner,
        private readonly CsvReader $csvReader,
        private readonly CsvWriter $csvWriter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('left-input', InputArgument::REQUIRED, 'Left input CSV file')
            ->addArgument('right-input', InputArgument::REQUIRED, 'Right input CSV file')
            ->addOption('left-on', null, InputOption::VALUE_REQUIRED, 'Column name to join on from the left file')
            ->addOption('right-on', null, InputOption::VALUE_REQUIRED, 'Column name to join on from the right file')
            ->addOption('output', null, InputOption::VALUE_REQUIRED, 'Output CSV file (defaults to STDOUT)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            // Creeaza obiectul de configurare cu coloanele de join
            $config = new TableInnerJoinerConfig(
                $input->getOption('left-on'),
                $input->getOption('right-on')
            );
            $this->tableInnerJoiner->configure($config);

            // Citeste cele doua fisiere de input in DataTables
            $leftTable = $this->csvReader->readClose(fopen($input->getArgument('left-input'), 'rb'));
            $rightTable = $this->csvReader->readClose(fopen($input->getArgument('right-input'), 'rb'));

            // Proceseaza datele
            [$outputTable] = $this->tableInnerJoiner->process($leftTable, $rightTable);

            // Scrie rezultatul
            $outputStream = $this->getOutputStream($input);
            if ($outputStream !== STDOUT) {
                $this->csvWriter->writeClose($outputTable, $outputStream);
                $io->success('Join completed successfully. Output written to ' . $input->getOption('output'));
            } else {
                $this->csvWriter->write($outputTable, $outputStream);
            }

        } catch (Throwable $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function getOutputStream(InputInterface $input)
    {
        $outputFile = $input->getOption('output');
        if (null === $outputFile) {
            return STDOUT;
        }
        return fopen($outputFile, 'wb');
    }
}
