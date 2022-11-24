<?php

namespace App\Tests\Provider;

use App\Entity\Genre;
use App\Provider\GenreProvider;
use App\Repository\GenreRepository;
use App\Transformer\OmdbGenreTransformer;
use PHPUnit\Framework\TestCase;

class GenreProviderTest extends TestCase
{
    public function testProviderReturnsGenerator(): void
    {
        $transformer = $this->getMockBuilder(OmdbGenreTransformer::class)
            ->disallowMockingUnknownTypes()
            ->onlyMethods(['transform'])
            ->getMock()
            ;
        $transformer->expects($this->exactly(2))->method('transform')->willReturn(new Genre());

        $mockRepository = $this->getMockBuilder(GenreRepository::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->onlyMethods(['findOneBy'])
            ->getMock()
            ;
        $mockRepository
            ->expects($this->exactly(2))
            ->method('findOneBy')
            ->willReturnCallback(function () {
                $args = func_get_args();
                if ($args[0] === ['name' => 'Action']) {
                    return (new Genre())->setName('Action');
                }
                return null;
            });
        $provider = new GenreProvider($transformer, $mockRepository);

        $genres = $provider->getGenresFromString('Foo, Bar');
        foreach ($genres as $genre) {
            $this->assertInstanceOf(Genre::class, $genre);
        }
        $this->assertInstanceOf(\Generator::class, $genres);
    }
}
