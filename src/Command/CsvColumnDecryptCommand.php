<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\Command;

use InvalidArgumentException;
use Sebihojda\Mbp\IO\CsvReader;
use Sebihojda\Mbp\IO\CsvWriter;
use Sebihojda\Mbp\TableProcessor\Config\ColumnDecryptorConfig;
use Sebihojda\Mbp\TableProcessor\Processor\ColumnDecryptor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'app:csv:column-decrypt',
    description: 'Decrypts a column of a CSV file with a private key.',
)]
class CsvColumnDecryptCommand extends Command
{
    public function __construct(
        private readonly ColumnDecryptor $columnDecryptor,
        private readonly CsvReader $csvReader,
        private readonly CsvWriter $csvWriter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('column', 'c', InputOption::VALUE_REQUIRED, 'Column to decrypt')
            ->addOption('private-key', 'k', InputOption::VALUE_REQUIRED, 'Private key PEM file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            $inputTable = $this->csvReader->read(STDIN);
            $config = $this->extractConfig($input);

            [$outputTable] = $this->columnDecryptor->configure($config)->process($inputTable);

            $this->csvWriter->write($outputTable, STDOUT);
        } catch (Throwable $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }

    private function extractConfig(InputInterface $input): ColumnDecryptorConfig
    {
        $column = $input->getOption('column');
        if (null === $column) {
            throw new InvalidArgumentException('Option --column is mandatory.');
        }

        $privateKeyFile = $input->getOption('private-key');
        if (null === $privateKeyFile) {
            throw new InvalidArgumentException('Option --private-key is mandatory.');
        }
        $privateKey = openssl_pkey_get_private(file_get_contents($privateKeyFile));

        return new ColumnDecryptorConfig($column, $privateKey);
    }
}
