<?php

namespace Sebihojda\Mbp\Command;

use Sebihojda\Mbp\IO\CsvReader;
use Sebihojda\Mbp\IO\CsvWriter;
use Sebihojda\Mbp\TableProcessor\Config\ColumnSignatureVerifierConfig;
use Sebihojda\Mbp\TableProcessor\Processor\ColumnSignatureVerifier;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'app:csv:column-sign-verify',
    description: 'Verifies a column of a CSV file with a public key and a signature',
)]
class CsvColumnSignVerifyCommand extends Command
{
    public function __construct(
        private readonly ColumnSignatureVerifier $columnSignatureVerifier,
        private readonly CsvReader $csvReader,
        private readonly CsvWriter $csvWriter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('column', 'c', InputOption::VALUE_REQUIRED, 'Column to sign')
            ->addOption('public-key', 'k', InputOption::VALUE_REQUIRED, 'Public key PEM file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            $inputTable = $this->csvReader->read(STDIN);
            [$outputTable] = $this->columnSignatureVerifier
                ->configure($this->extractConfig($input))
                ->process($inputTable);
            $this->csvWriter->write($outputTable, STDOUT);
        } catch (Throwable $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function extractConfig(InputInterface $input): ColumnSignatureVerifierConfig
    {
        $column = $input->getOption('column');
        if (null === $column) {
            throw new InvalidArgumentException('The `column` option is missing');
        }
        $signatureColumn = $column.'_signature';

        $publicKeyFile = $input->getOption('public-key');
        if (null === $publicKeyFile) {
            throw new InvalidArgumentException('The `public-key` option is missing');
        }
        $publicKey = openssl_pkey_get_public(file_get_contents($publicKeyFile));

        return new ColumnSignatureVerifierConfig($column, $signatureColumn, $publicKey);
    }
}
