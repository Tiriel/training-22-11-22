<?php

namespace App\Tests\Transformer;

use App\Entity\Movie;
use App\Transformer\OmdbMovieTransformer;
use PHPUnit\Framework\TestCase;

class OmdbMovieTransformerTest extends TestCase
{
    public function testSomething(): void
    {
        $data = [
            'Title' => 'Star Wars',
            'Poster' => 'https://foo.com',
            'Country' => 'United States',
            'Rated' => 'PG',
            'imdbID' => 'tt12984208',
            'Released' => '25 May 1977',
        ];

        $transformer = new OmdbMovieTransformer();
        $actual = $transformer->transform($data);

        $this->assertInstanceOf(Movie::class, $actual);
        $this->assertSame('Star Wars', $actual->getTitle());
        $this->assertInstanceOf(\DateTimeImmutable::class, $actual->getReleasedAt());
    }

    public function testTransformerTakesYearWhenReleasedNotAvailable()
    {
        $data = [
            'Title' => 'Star Wars',
            'Poster' => 'https://foo.com',
            'Country' => 'United States',
            'Rated' => 'PG',
            'imdbID' => 'tt12984208',
            'Released' => 'N/A',
            'Year' => '1977',
        ];

        $transformer = new OmdbMovieTransformer();
        $actual = $transformer->transform($data);

        $this->assertInstanceOf(Movie::class, $actual);
        $this->assertSame('Star Wars', $actual->getTitle());
        $this->assertInstanceOf(\DateTimeImmutable::class, $actual->getReleasedAt());
        $this->assertSame(
            (new \DateTimeImmutable('01-01-1977'))->format('Y'),
            $actual->getReleasedAt()->format('Y')
        );
    }
}
