<?php

namespace App\EventSubscriber;

use App\Event\LogsEvent;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class LogsSubscriber implements EventSubscriber
{
    public function onKernelRequest(RequestEvent $event)
    {
       //
    }

    public function onLog($data)
    {
        //
    }

    public function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => 'onKernelRequest',
            LogsEvent::NAME => 'onLog'
        ];
    }
}
