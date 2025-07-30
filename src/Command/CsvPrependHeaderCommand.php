<?php

namespace Sebihojda\Mbp\Command;

use Sebihojda\Mbp\IO\CsvReader;
use Sebihojda\Mbp\IO\CsvWriter;
use Sebihojda\Mbp\IO\JsonWriter;
use Sebihojda\Mbp\TableProcessor\Config\TableHeaderPrependerConfig;
use Sebihojda\Mbp\TableProcessor\Processor\TableHeaderPrepender;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'app:csv:prepend-header',
    description: 'Prepends a list of headers to a CSV file',
)]
class CsvPrependHeaderCommand extends Command
{
    public function __construct(
        private readonly TableHeaderPrepender $tableHeaderPrepender,
        private readonly CsvReader $csvReader,
        private readonly CsvWriter $csvWriter,
        private readonly JsonWriter $jsonWriter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('headers', 't', InputOption::VALUE_REQUIRED, 'Comma-separated list of headers to prepend')
            ->addOption('output-format', 'o', InputOption::VALUE_REQUIRED, 'Output format (csv|json)', 'csv');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            $inputTable = $this->csvReader->read(STDIN, true);
            $config = $this->extractConfig($input);
            [$outputTable] = $this->tableHeaderPrepender
                ->configure($config)
                ->process($inputTable);
            if ($config->getOutputFormat() === TableHeaderPrependerConfig::OUTPUT_FORMAT_CSV) {
                $this->csvWriter->write($outputTable, STDOUT);
            } elseif ($config->getOutputFormat() === TableHeaderPrependerConfig::OUTPUT_FORMAT_JSON) {
                $this->jsonWriter->write($outputTable, STDOUT);
            }
        } catch (Throwable $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function extractConfig(InputInterface $input): TableHeaderPrependerConfig
    {
        $headers = $input->getOption('headers');
        if (null === $headers) {
            throw new InvalidArgumentException('The `headers` option is missing');
        }
        $outputFormat = $input->getOption('output-format');
        if (null === $outputFormat) {
            throw new InvalidArgumentException('The `output-format` option is missing');
        }

        return new TableHeaderPrependerConfig(explode(',', $headers), $outputFormat);
    }
}
