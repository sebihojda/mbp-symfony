<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\Command;

use InvalidArgumentException;
use Sebihojda\Mbp\IO\CsvReader;
use Sebihojda\Mbp\IO\CsvWriter;
use Sebihojda\Mbp\TableProcessor\Config\ColumnEncryptorConfig;
use Sebihojda\Mbp\TableProcessor\Processor\ColumnEncryptor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'app:csv:column-encrypt',
    description: 'Encrypts a column of a CSV file with a public key.',
)]
class CsvColumnEncryptCommand extends Command
{
    public function __construct(
        private readonly ColumnEncryptor $columnEncryptor,
        private readonly CsvReader $csvReader,
        private readonly CsvWriter $csvWriter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('column', 'c', InputOption::VALUE_REQUIRED, 'Column to encrypt')
            ->addOption('public-key', 'k', InputOption::VALUE_REQUIRED, 'Public key PEM file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            $inputTable = $this->csvReader->read(STDIN);
            $config = $this->extractConfig($input);

            [$outputTable] = $this->columnEncryptor->configure($config)->process($inputTable);

            $this->csvWriter->write($outputTable, STDOUT);
        } catch (Throwable $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }

    private function extractConfig(InputInterface $input): ColumnEncryptorConfig
    {
        $column = $input->getOption('column');
        if (null === $column) {
            throw new InvalidArgumentException('Option --column is mandatory.');
        }

        $publicKeyFile = $input->getOption('public-key');
        if (null === $publicKeyFile) {
            throw new InvalidArgumentException('Option --public-key is mandatory.');
        }
        $publicKey = openssl_pkey_get_public(file_get_contents($publicKeyFile));

        return new ColumnEncryptorConfig($column, $publicKey);
    }
}
