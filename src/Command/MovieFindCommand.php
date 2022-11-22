<?php

namespace App\Command;

use App\Consumer\OMDbApiConsumer;
use App\Entity\Movie;
use App\Provider\MovieProvider;
use App\Repository\MovieRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsCommand(
    name: 'app:movie:find',
    description: 'Add a short description for your command',
)]
class MovieFindCommand extends Command
{
    private ?SymfonyStyle $io = null;

    public function __construct(
        private MovieRepository $movieRepository,
        private MovieProvider $provider,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('value', InputArgument::OPTIONAL, 'The movie you are searching for.')
            ->addArgument('type', InputArgument::OPTIONAL, 'The type of search (title or id).')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = $io = new SymfonyStyle($input, $output);
        $this->provider->setSymfonyStyle($io);

        if (!$value = $input->getArgument('value')) {
            $value = $io->ask('What is the title or id of the movie you\'re searching for?');
        }

        $type = strtolower($input->getArgument('type'));
        if (\in_array($type, ['title', 'id'])) {
            $type = substr($type, 0, 1);
        }
        if (!\in_array($type, ['t', 'i'])) {
            $type = $io->choice('What type of data are your searching on?', ['t' => 'title', 'i' => 'id'], 't');
        }

        $io->title('Your search :');
        $io->text(sprintf("Searching for a movie with %s=%s", $type, $value));
        if (OMDbApiConsumer::MODE_ID === $type && $movie = $this->movieRepository->findOneBy(['imdbId' => $value])) {
            $this->displayResult($movie);

            return Command::SUCCESS;
        }

        try {
            $movie = $this->provider->getMovie($type, $value);
        } catch (NotFoundHttpException) {
            $io->error('Movie not found!');

            return Command::FAILURE;
        }

        $this->displayResult($movie);

        return Command::SUCCESS;
    }

    private function displayResult(Movie $movie): void
    {
        $this->io->section('Result :');
        $this->io->table(['id', 'imdbId', 'Title', 'Rated'],[
            [$movie->getId(), $movie->getImdbId(), $movie->getTitle(), $movie->getRated()],
        ]);

        $this->io->success('Movie successfully found and imported!');
    }
}
