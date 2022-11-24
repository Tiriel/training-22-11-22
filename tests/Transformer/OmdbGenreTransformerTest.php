<?php

namespace App\Tests\Transformer;

use App\Entity\Genre;
use App\Transformer\OmdbGenreTransformer;
use PHPUnit\Framework\TestCase;

class OmdbGenreTransformerTest extends TestCase
{
    public function testTransformerReturnsGenreObject(): void
    {
        $transformer = new OmdbGenreTransformer();
        $actual = $transformer->transform('Action');

        $this->assertInstanceOf(Genre::class, $actual);
    }
}
