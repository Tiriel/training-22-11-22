<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class DefaultControllerTest extends WebTestCase
{
    private static KernelBrowser $client;

    public static function setUpBeforeClass(): void
    {
        static::$client = static::createClient();
        //$repository = static::getContainer()->get(UserRepository::class);
        //static::$client->loginUser($repository->findOneBy(['email' => 'john.doe@example.org']));
    }

    /**
     * @dataProvider providerStaticUrls
     * @group smoke
     */
    public function testPublicUrlIsNotServerError(string $method, string $url): void
    {
        static::$client->request($method, $url);
        if (\in_array(static::$client->getResponse()->getStatusCode(), [301, 302, 307, 308])) {
            static::$client->followRedirect();
        }

        $this->assertSame(200, static::$client->getResponse()->getStatusCode());
    }

    public function providerStaticUrls(): array
    {
        return [
            'contact' => ['GET', '/contact'],
            'book' => ['GET', '/book'],
        ];
    }

    public function providePublicUrlsAndStatusCodes(): \Generator
    {
        $router = static::getContainer()->get(RouterInterface::class);
        $collection = $router->getRouteCollection();
        static::ensureKernelShutdown();

        foreach ($collection as $routeName => $route) {
            /** @var Route $route */
            $variables = $route->compile()->getVariables();
            if (count(array_diff($variables, $route->getDefaults())) > 0) {
                continue;
            }
            if ([] === $methods = $route->getMethods()) {
                $methods[] = 'GET';
            }
            foreach ($methods as $method) {
                $path = $router->generate($routeName);
                yield "$method $path" => [$method, $path];
            }
        }
    }
}
