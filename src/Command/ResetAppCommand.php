<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

#[AsCommand(name: 'app:reset')]
class ResetAppCommand extends Command
{

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Install dependencies
        $composerInstall = new Process(['composer', 'install', '-n']);
        $composerInstall->run();
        if (!$composerInstall->isSuccessful()) {
            $io->error(['composer install command failed']);
            return Command::FAILURE;
        }

        // Remove existing tables
        $schemaDrop = new ArrayInput([
            'command' => 'doctrine:schema:drop',
            '--force' => true
        ]);
        $schemaDropCode = $this->getApplication()->doRun($schemaDrop, $output);

        if ($schemaDropCode !== 0) {
            return Command::FAILURE;
        }

        // Recreate tables
        $schemaCreate = new ArrayInput([
            'command' => 'doctrine:schema:create',
        ]);
        $schemaCreateCode = $this->getApplication()->doRun($schemaCreate, $output);

        if ($schemaCreateCode !== 0) {
            return Command::FAILURE;
        }

        // Load fixtures
        $loadFixtures = new ArrayInput([
            'command' => 'doctrine:fixtures:load'
        ]);
        $loadFixtures->setInteractive(false);
        $loadFixturesCode = $this->getApplication()->doRun($loadFixtures, $output);

        if ($loadFixturesCode !== 0) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}