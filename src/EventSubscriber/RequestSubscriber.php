<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class RequestSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig,
        private bool $isDown
    ) {}

    public function onKernelRequest(RequestEvent $event)
    {
        if ($this->isDown) {
            $response = new Response();
            if ($event->isMainRequest()) {
                $response->setContent($this->twig->render('down.html.twig'));
            }
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 9999]
        ];
    }
}