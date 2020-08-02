<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ApiExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof NotFoundHttpException) {
            // let react handle not found exception
            $event->setResponse(new RedirectResponse('/'));

            return;
        }

        if (!($exception instanceof HttpException) || false === strpos($event->getRequest()->getRequestUri(), '/api/')) {
            return;
        }

        $response = new JsonResponse(['error' => $exception->getMessage()]);
        $response->setStatusCode($exception->getStatusCode());
        $event->setResponse($response);
    }
}
