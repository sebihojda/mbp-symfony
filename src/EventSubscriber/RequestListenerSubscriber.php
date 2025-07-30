<?php

namespace Sebihojda\Mbp\EventSubscriber;

/*use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class RequestListenerSubscriber implements EventSubscriberInterface
{
    public function onRequestEvent(RequestEvent $event): void
    {
        // ...
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onRequestEvent',
        ];
    }
}*/

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestListenerSubscriber implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event): void
    {
        dump('RequestListenerSubscriber executed!');

        // Ne asiguram ca rulam doar pentru cererea principala, nu sub-cereri
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        // Adaugam un header custom pe obiectul Request
        $request->headers->set('X-Modified-By', 'MyEventListener');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Ascultam evenimentul KernelEvents::REQUEST si apelam metoda onKernelRequest
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
