<?php

namespace App\Provider;

use App\Consumer\OMDbApiConsumer;
use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Transformer\OmdbMovieTransformer;
use Symfony\Component\Console\Style\SymfonyStyle;

class MovieProvider
{
    private ?SymfonyStyle $io = null;

    public function __construct(
        private OMDbApiConsumer $consumer,
        private OmdbMovieTransformer $movieTransformer,
        private MovieRepository $repository,
        private GenreProvider $genreProvider
    ) {}

    public function getMovie(string $type, string $value): Movie
    {
        $this->io?->text('Searching for base information onOMDb API.');
        $data = $this->consumer->consume($type, $value);
        $this->io?->text('Movie found.');

        if ($movie = $this->repository->findOneBy(['title' => $data['Title']])) {
            $this->io?->note('Movie already in Database!');
            return $movie;
        }

        $movie = $this->movieTransformer->transform($data);
        foreach ($this->genreProvider->getGenresFromString($data['Genre']) as $genre) {
            $movie->addGenre($genre);
        }

        $this->io?->section('Saving new movie in database');
        $this->repository->add($movie, true);
        $this->io?->text('Movie saved.');

        return $movie;
    }

    public function setSymfonyStyle(?SymfonyStyle $io): void
    {
        $this->io = $io;
    }
}