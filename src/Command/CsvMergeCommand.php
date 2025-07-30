<?php

namespace Sebihojda\Mbp\Command;

use Sebihojda\Mbp\IO\CsvReader;
use Sebihojda\Mbp\IO\CsvWriter;
use Sebihojda\Mbp\TableProcessor\Processor\TableMerger;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'app:csv:merge',
    description: 'Merges a list of CSV files into a single one',
)]
class CsvMergeCommand extends Command
{
    public function __construct(
        private readonly TableMerger $tableMerger,
        private readonly CsvReader $csvReader,
        private readonly CsvWriter $csvWriter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('input', InputArgument::IS_ARRAY, 'Input CSV files', [])
            ->addOption('output', null, InputOption::VALUE_REQUIRED, 'Output CSV file')
            ->addOption('no-headers', null, InputOption::VALUE_NONE, 'Consider input files are missing headers');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            // read the input files into model objects
            $inputTables = [];
            foreach ($this->getInputStreams($input) as $inputStream) {
                $inputTables[] = $this->csvReader->readClose($inputStream, $this->hasNoHeaders($input));
            }

            [$outputTable] = $this->tableMerger->process(...$inputTables);

            $outputStream = $this->getOutputStream($input);
            if ($outputStream !== STDOUT) {
                $this->csvWriter->writeClose($outputTable, $outputStream);
            } else {
                $this->csvWriter->write($outputTable, $outputStream);
            }
        } catch (Throwable $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function getInputStreams(InputInterface $input): array
    {
        $inputFiles = $input->getArgument('input');
        if (empty($inputFiles)) {
            throw new InvalidArgumentException('At least one input file must be provided');
        }

        return array_map(static fn($i) => fopen($i, 'rb'), $inputFiles);
    }

    private function getOutputStream(InputInterface $input)
    {
        $outputFile = $input->getOption('output');
        if (null === $outputFile) {
            return STDOUT;
        }

        return fopen($outputFile, 'wb');
    }

    private function hasNoHeaders(InputInterface $input): bool
    {
        return $input->getOption('no-headers');
    }
}

