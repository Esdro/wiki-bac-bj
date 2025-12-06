<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $data = [];
        $throwable = $event->getThrowable();
        if ($throwable instanceof HttpException) {
            $statusCode = $throwable->getStatusCode();
            $message = $throwable->getMessage();
            // You can add custom logic here, such as logging or modifying the response
            $data = [
                'status' => $statusCode,
                'message' => $message,
            ];
        } else {
            $message = $throwable->getTrace();
            $data = [
                'status' => 500,
                'message' => $message
            ];
        }
        $response = new \Symfony\Component\HttpFoundation\JsonResponse($data, $data['status']);
        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onExceptionEvent',
        ];
    }
}
