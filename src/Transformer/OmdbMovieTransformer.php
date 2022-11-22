<?php

namespace App\Transformer;

use App\Entity\Movie;
use Symfony\Component\Form\DataTransformerInterface;

class OmdbMovieTransformer implements DataTransformerInterface
{
    public function transform(mixed $value)
    {
        $date = $value['Released'] === 'N/A' ? $value['Year'] : $value['Released'];

        return (new Movie())
            ->setTitle($value['Title'])
            ->setPoster($value['Poster'])
            ->setCountry($value['Country'])
            ->setReleasedAt(new \DateTimeImmutable($date))
            ->setPrice("5.0")
            ;
    }

    public function reverseTransform(mixed $value)
    {
        throw new \RuntimeException("Not implemented.");
    }
}