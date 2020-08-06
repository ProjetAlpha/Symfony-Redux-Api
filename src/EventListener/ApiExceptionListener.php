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

        $response = new JsonResponse(['error' => $exception->getMessage()]);
        $response->setStatusCode($exception->getStatusCode());
        $event->setResponse($response);
    }
}
