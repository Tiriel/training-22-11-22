<?php

namespace App\Tests\Transformer;

use App\Entity\Movie;
use App\Transformer\OmdbMovieTransformer;
use PHPUnit\Framework\TestCase;

class OmdbMovieTransformerTest extends TestCase
{
    private static OmdbMovieTransformer $transformer;

    public static function setUpBeforeClass(): void
    {
        static::$transformer = new OmdbMovieTransformer();
    }

    /**
     * @dataProvider provideDataForTransformMethod
     */
    public function testTransformerReturnsMovieEntity(array $data): void
    {
        $actual = static::$transformer->transform($data);

        $this->assertInstanceOf(Movie::class, $actual);
        $this->assertSame('Star Wars', $actual->getTitle());
        $this->assertInstanceOf(\DateTimeImmutable::class, $actual->getReleasedAt());
        $this->assertSame(
            (new \DateTimeImmutable('01-01-1977'))->format('Y'),
            $actual->getReleasedAt()->format('Y')
        );
    }

    public function provideDataForTransformMethod()
    {
        return [
            'Released' => [[
                'Title' => 'Star Wars',
                'Poster' => 'https://foo.com',
                'Country' => 'United States',
                'Rated' => 'PG',
                'imdbID' => 'tt12984208',
                'Released' => '25 May 1977',
                'Year' => '1977',
            ]],
            'Year' => [[
                'Title' => 'Star Wars',
                'Poster' => 'https://foo.com',
                'Country' => 'United States',
                'Rated' => 'PG',
                'imdbID' => 'tt12984208',
                'Released' => 'N/A',
                'Year' => '1977',
            ]]
        ];
    }

    public function testTransformerThrowsInvalidArgumentWhenNotArray()
    {
        $this->expectException(\InvalidArgumentException::class);

        $transformer = new OmdbMovieTransformer();
        $transformer->transform('Action');
    }

    public function testTransformerThrowsInvalidArgumentExceptionWhenMissingKey()
    {
        $this->expectException(\InvalidArgumentException::class);

        $transformer = new OmdbMovieTransformer();
        $transformer->transform([]);
    }
}
