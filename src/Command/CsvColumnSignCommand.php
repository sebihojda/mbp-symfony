<?php

namespace Sebihojda\Mbp\Command;

use Sebihojda\Mbp\IO\CsvReader;
use Sebihojda\Mbp\IO\CsvWriter;
use Sebihojda\Mbp\TableProcessor\Config\ColumnSignerConfig;
use Sebihojda\Mbp\TableProcessor\Processor\ColumnSigner;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'app:csv:column-sign',
    description: 'Signs a column of a CSV file with a private key',
)]
class CsvColumnSignCommand extends Command
{
    public function __construct(
        private readonly ColumnSigner $columnSigner,
        private readonly CsvReader $csvReader,
        private readonly CsvWriter $csvWriter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('column', 'c', InputOption::VALUE_REQUIRED, 'Column to sign')
            ->addOption('private-key', 'k', InputOption::VALUE_REQUIRED, 'Private key PEM file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            $inputTable = $this->csvReader->read(STDIN);
            [$outputTable] = $this->columnSigner
                ->configure($this->extractConfig($input))
                ->process($inputTable);
            $this->csvWriter->write($outputTable, STDOUT);
        } catch (Throwable $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function extractConfig(InputInterface $input): ColumnSignerConfig
    {
        $column = $input->getOption('column');
        if (null === $column) {
            throw new InvalidArgumentException('The `column` option is missing');
        }
        $signatureColumn = $column.'_signature';

        $privateKeyFile = $input->getOption('private-key');
        if (null === $privateKeyFile) {
            throw new InvalidArgumentException('The `private-key` option is missing');
        }
        $privateKey = openssl_pkey_get_private(file_get_contents($privateKeyFile));

        return new ColumnSignerConfig($column, $signatureColumn, $privateKey);
    }
}
