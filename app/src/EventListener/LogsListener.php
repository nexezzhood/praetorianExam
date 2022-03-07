<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class LogsListener
{
    public function onKernelRequest(RequestEvent $event)
    {
        //
    }

    public function onKernelRequestBlocker(RequestEvent $event)
    {
        //
    }

    public function onKernelException(ExceptionEvent $event)
    {
        //
    }
}