<?php

namespace App\Transformer;

use App\Entity\Genre;
use Symfony\Component\Form\DataTransformerInterface;

class OmdbGenreTransformer implements DataTransformerInterface
{
    public function transform(mixed $value)
    {
        return (new Genre())->setName($value);
    }

    public function reverseTransform(mixed $value)
    {
        throw new \RuntimeException("Not implemented.");
    }
}