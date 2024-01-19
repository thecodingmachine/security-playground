<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

#[AsCommand(name: 'app:reset')]
class ResetAppCommand extends Command
{

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Install dependencies
        $composerInstall = new Process(['composer', 'install', '-n']);
        $composerInstall->mustRun();

        if (!$composerInstall->isSuccessful()) {
            echo $composerInstall->getOutput();
            return Command::FAILURE;
        }

        // Remove existing tables
        $schemaDrop = new Process(['bin/console', 'doctrine:schema:drop', '--force']);
        $schemaDrop->mustRun();
        echo $schemaDrop->getOutput();

        if (!$schemaDrop->isSuccessful()) {
            echo $schemaDrop->getOutput();
            return Command::FAILURE;
        }

        // Recreate tables
        $schemaCreate = new Process(['bin/console', 'doctrine:schema:create']);
        $schemaCreate->mustRun();
        echo $schemaCreate->getOutput();

        if (!$schemaCreate->isSuccessful()) {
            echo $schemaCreate->getOutput();
            return Command::FAILURE;
        }

        // Load fixtures
        $loadFixtures = new Process(['bin/console', 'doctrine:fixtures:load', '-n']);
        $loadFixtures->mustRun();
        echo $loadFixtures->getOutput();

        if (!$loadFixtures->isSuccessful()) {
            echo $loadFixtures->getOutput();
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}