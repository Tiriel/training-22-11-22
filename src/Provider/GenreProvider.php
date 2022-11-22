<?php

namespace App\Provider;

use App\Repository\GenreRepository;
use App\Transformer\OmdbGenreTransformer;

class GenreProvider
{
    public function __construct(
        private OmdbGenreTransformer $transformer,
        private GenreRepository $repository
    ) {}

    public function getGenresFromString(string $genres): \Generator
    {
        $names = \explode(', ', $genres);

        foreach ($names as $name) {
            yield $this->repository->findOneBy(['name' => $name])
                ?? $this->transformer->transform($name);
        }
    }
}