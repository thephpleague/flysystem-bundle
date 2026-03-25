<?php

/*
 * This file is part of the flysystem-bundle project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\FlysystemBundle\Command;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ServiceLocator;

abstract class AbstractTransferCommand extends Command
{
    public function __construct(private readonly ServiceLocator $storages)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('storage', InputArgument::REQUIRED, 'The configured Flysystem storage name.')
            ->addArgument('source', InputArgument::REQUIRED, 'The source path to transfer.')
            ->addArgument('destination', InputArgument::OPTIONAL, 'The destination path. Defaults to the source basename.')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Overwrite the destination file if it already exists.');
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        if (null === $input->getArgument('storage')) {
            $input->setArgument('storage', $io->askQuestion($this->createStorageQuestion()));
        }

        if (null === $input->getArgument('source')) {
            $input->setArgument('source', $io->askQuestion($this->createRequiredQuestion(
                'What is the source path to transfer?',
                'The source path cannot be empty.'
            )));
        }

        if (null === $input->getArgument('destination')) {
            $input->setArgument('destination', $io->askQuestion($this->createDestinationQuestion((string) $input->getArgument('source'))));
        }
    }

    final protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $storageName = (string) $input->getArgument('storage');
        $source = (string) $input->getArgument('source');
        $destination = $input->getArgument('destination');
        $destination = null === $destination ? basename($source) : (string) $destination;
        $destination = $this->normalizeDestination($source, $destination);

        $force = (bool) $input->getOption('force');

        try {
            $storage = $this->getStorage($storageName);
            $this->transfer($storage, $source, $destination, $force);
        } catch (InvalidArgumentException|\InvalidArgumentException $exception) {
            $io->error($exception->getMessage());

            return self::INVALID;
        } catch (FilesystemException|\RuntimeException $exception) {
            $io->error($exception->getMessage());

            return self::FAILURE;
        }

        $io->success($this->createSuccessMessage($storageName, $source, $destination));

        return self::SUCCESS;
    }

    private function createStorageQuestion(): Question
    {
        $storageNames = array_keys($this->storages->getProvidedServices());
        sort($storageNames);

        $question = new Question('Which configured Flysystem storage should be used?', 1 === count($storageNames) ? $storageNames[0] : null);
        $question->setAutocompleterValues($storageNames);
        $question->setValidator(function (?string $answer): string {
            $answer = trim((string) $answer);

            if ('' === $answer) {
                throw new \RuntimeException('The storage name cannot be empty.');
            }

            if (!$this->storages->has($answer)) {
                throw new \RuntimeException(sprintf('The storage "%s" does not exist.', $answer));
            }

            return $answer;
        });

        return $question;
    }

    private function createDestinationQuestion(string $source): Question
    {
        $question = new Question('What is the destination path?', basename($source));
        $question->setValidator(function (?string $answer): string {
            $answer = trim((string) $answer);

            if ('' === $answer) {
                throw new \RuntimeException('The destination path cannot be empty.');
            }

            return $answer;
        });

        return $question;
    }

    private function createRequiredQuestion(string $label, string $errorMessage): Question
    {
        $question = new Question($label);
        $question->setValidator(function (?string $answer) use ($errorMessage): string {
            $answer = trim((string) $answer);

            if ('' === $answer) {
                throw new \RuntimeException($errorMessage);
            }

            return $answer;
        });

        return $question;
    }

    private function getStorage(string $storageName): FilesystemOperator
    {
        if (!$this->storages->has($storageName)) {
            throw new InvalidArgumentException(sprintf('The storage "%s" does not exist.', $storageName));
        }

        $storage = $this->storages->get($storageName);
        if (!$storage instanceof FilesystemOperator) {
            throw new \RuntimeException(sprintf('The storage "%s" is not a Flysystem operator.', $storageName));
        }

        return $storage;
    }

    protected function normalizeDestination(string $source, string $destination): string
    {
        return $destination;
    }

    abstract protected function transfer(FilesystemOperator $storage, string $source, string $destination, bool $force = false): void;

    abstract protected function createSuccessMessage(string $storageName, string $source, string $destination): string;
}
