<?php

namespace App\Transformer;

use App\Entity\Movie;
use Symfony\Component\Form\DataTransformerInterface;

class OmdbMovieTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): Movie
    {
        $date = $value['Released'] === 'N/A' ? $value['Year'] : $value['Released'];

        return (new Movie())
            ->setTitle($value['Title'])
            ->setPoster($value['Poster'])
            ->setCountry($value['Country'])
            ->setReleasedAt(new \DateTimeImmutable($date))
            ->setRated($value['Rated'])
            ->setImdbId($value['imdbID'])
            ->setPrice("5.0")
            ;
    }

    public function reverseTransform(mixed $value): mixed
    {
        throw new \RuntimeException("Not implemented.");
    }
}