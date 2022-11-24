<?php

namespace App\Transformer;

use App\Entity\Genre;
use Symfony\Component\Form\DataTransformerInterface;

class OmdbGenreTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): Genre
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException();
        }

        return (new Genre())->setName($value);
    }

    public function reverseTransform(mixed $value): mixed
    {
        throw new \RuntimeException("Not implemented.");
    }
}