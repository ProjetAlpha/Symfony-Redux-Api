<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

final class ApiExceptionListener
{
    protected $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // let react router handle not found exception : react router should handle non-api routes.
        if ($exception instanceof NotFoundHttpException && false === strpos($event->getRequest()->getRequestUri(), '/api/')) {
            $response = new Response();
            $response->setContent($this->twig->render('base.html.twig'));
            $response->setStatusCode(Response::HTTP_OK);

            $event->setResponse($response);

            return;
        }

        if (!($exception instanceof HttpException) || false === strpos($event->getRequest()->getRequestUri(), '/api/')) {
            return;
        }

        // standard server error
        $response = new JsonResponse(['error' => $exception->getMessage()]);

        // if an unauthorized response is triggered send a refresh token
        if ($exception->getStatusCode() == Response::HTTP_UNAUTHORIZED && $exception->getHeaders()) {
            $response = new JsonResponse(['refresh_token' => $exception->getHeaders()['refresh_token']]) ?? $response;
        }

        $response->setStatusCode($exception->getStatusCode());
        $event->setResponse($response);
    }
}
