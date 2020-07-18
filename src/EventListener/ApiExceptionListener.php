<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ApiExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!($exception instanceof HttpException) || false === strpos($event->getRequest()->getRequestUri(), '/api/')) {
            return;
        }

        $response = new JsonResponse(['error' => $exception->getMessage()]);
        $response->setStatusCode($exception->getStatusCode());
        $event->setResponse($response);
    }
}
