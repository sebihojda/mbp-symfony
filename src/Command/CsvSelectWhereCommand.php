<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\Command;

use Sebihojda\Mbp\IO\CsvReader;
use Sebihojda\Mbp\IO\CsvWriter;
use Sebihojda\Mbp\TableProcessor\Config\TableSelectWhereConfig;
use Sebihojda\Mbp\TableProcessor\Processor\TableSelectWhereProcessor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'app:csv:select',
    description: 'Selects columns and filters rows based on where clauses.',
)]
class CsvSelectWhereCommand extends Command
{
    public function __construct(
        private readonly TableSelectWhereProcessor $processor,
        private readonly CsvReader $csvReader,
        private readonly CsvWriter $csvWriter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('input', InputArgument::REQUIRED, 'Input CSV file')
            ->addOption('select', null, InputOption::VALUE_REQUIRED, 'Comma-separated list of columns to select (e.g., "name,city")')
            ->addOption('where', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Filter clause (e.g., "age > 30", "city = Bucuresti", "name ~ /Popescu$/")')
            ->addOption('output', null, InputOption::VALUE_REQUIRED, 'Output CSV file (defaults to STDOUT)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            // Parseaza coloanele de selectat
            $selectColumns = $input->getOption('select') ? explode(',', $input->getOption('select')) : [];

            // Parseaza clauzele "where"
            $whereClauses = [];
            foreach ($input->getOption('where') as $clauseString) {
                if (preg_match('/^(.+?)\s*([<>=~])\s*(.+)$/', $clauseString, $matches)) {
                    $whereClauses[] = [
                        'column' => trim($matches[1]),
                        'operator' => $matches[2],
                        'value' => trim($matches[3]),
                    ];
                } else {
                    throw new \InvalidArgumentException("Where clause is invalid: '$clauseString'");
                }
            }

            $config = new TableSelectWhereConfig($selectColumns, $whereClauses);
            $this->processor->configure($config);

            $dataTable = $this->csvReader->readClose(fopen($input->getArgument('input'), 'rb'));
            [$outputTable] = $this->processor->process($dataTable);

            $outputStream = $this->getOutputStream($input);
            $this->csvWriter->write($outputTable, $outputStream);
            if ($outputStream !== STDOUT) {
                fclose($outputStream);
                $io->success('Select & Where completed. Output written to ' . $input->getOption('output'));
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
