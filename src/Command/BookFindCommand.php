<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:book:find',
    description: 'Add a short description for your command',
)]
class BookFindCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('lastname', InputArgument::OPTIONAL, 'Argument description')
            ->addArgument('firstname', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NEGATABLE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('lastname');
        if (!$arg1){
            $arg1 = $io->ask('What is your first argument?', 'Bob');
        }

        if ($arg1) {
            $io->note(sprintf('You passed an lastname: %s', $arg1));
        }

        $arg2 = $input->getArgument('firstname');
        if (!$arg2) {
            $arg2 = $io->choice('What is the second argument?', ['first' => 'first', 'second' => 'second'], 'first');
        }

        if ($arg2) {
            $io->note(sprintf('You passed an firstname: %s',  $arg2));
        }

        if (null !== ($value = $input->getOption('option1'))) {
            if ($value === true) {
                $value = 'True';
            } elseif ($value === false) {
                $value = 'False';
            }
            $io->info('You sent and option : '. $value);
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
