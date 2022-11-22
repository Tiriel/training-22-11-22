<?php

namespace App\Provider;

use App\Consumer\OMDbApiConsumer;
use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Transformer\OmdbMovieTransformer;

class MovieProvider
{
    public function __construct(
        private OMDbApiConsumer $consumer,
        private OmdbMovieTransformer $movieTransformer,
        private MovieRepository $repository,
        private GenreProvider $genreProvider
    ) {}

    public function getMovie(string $type, string $value): Movie
    {
        $data = $this->consumer->consume($type, $value);

        if ($movie = $this->repository->findOneBy(['title' => $data['Title']])) {
            return $movie;
        }

        $movie = $this->movieTransformer->transform($data);
        foreach ($this->genreProvider->getGenresFromString($data['Genre']) as $genre) {
            $movie->addGenre($genre);
        }

        $this->repository->add($movie, true);

        return $movie;
    }
}