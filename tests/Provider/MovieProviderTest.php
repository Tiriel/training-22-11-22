<?php

namespace App\Tests\Provider;

use App\Consumer\OMDbApiConsumer;
use App\Entity\Movie;
use App\Provider\GenreProvider;
use App\Provider\MovieProvider;
use App\Repository\MovieRepository;
use App\Transformer\OmdbMovieTransformer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;

class MovieProviderTest extends TestCase
{
    public function testMovieProviderReturnsMovieEntity(): void
    {
        $consumer = $this->getMockConsumer();
        $transformer = $this->getMockTransformer();
        $repository = $this->getMockRepository();
        $genreProvider = $this->getMockGenreProvider();
        $security = $this->getMockSecurity();
        $provider = new MovieProvider(
            $consumer,
            $transformer,
            $repository,
            $genreProvider,
            $security
        );
        $actual = $provider->getMovie(OMDbApiConsumer::MODE_TITLE, 'Star Wars');

        $this->assertInstanceOf(Movie::class, $actual);
    }

    private function getMockConsumer(): OMDbApiConsumer|MockObject
    {
        $mock = $this->createMock(OMDbApiConsumer::class);
        $mock->expects($this->once())
            ->method('consume')
            ->willReturn(['Title' => 'Star Wars', 'Genre' => 'Action'])
            ;
        return $mock;
    }

    private function getMockTransformer(): OmdbMovieTransformer
    {
        $mock = $this->createMock(OmdbMovieTransformer::class);
        $mock->expects($this->once())
            ->method('transform')
            ->willReturn(new Movie())
            ;

        return $mock;
    }

    private function getMockRepository(): MovieRepository
    {
        $mock = $this->createMock(MovieRepository::class);
        $mock->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null)
            ;
        $mock->expects($this->once())
            ->method('add')
            ;

        return $mock;
    }

    private function getMockGenreProvider(): GenreProvider
    {
        return $this->createMock(GenreProvider::class);
    }

    private function getMockSecurity(): Security
    {
        $mock = $this->createMock(Security::class);
        $mock->expects($this->once())->method('getUser')->willReturn(null);

        return $mock;
    }
}
