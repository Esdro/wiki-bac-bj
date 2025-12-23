<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        $statusCode = 500;
        $data = [
            'status' => $statusCode,
            'message' => 'Une erreur est survenue'
        ];

        if ($throwable instanceof HttpException) {
            $statusCode = $throwable->getStatusCode();
            $message = $throwable->getMessage();

            // Essayer de parser le message si c'est du JSON (violations)
            $decodedMessage = json_decode($message, true);
//            var_dump($decodedMessage);
            if (is_array($decodedMessage)) {
                $data = [
                    'status' => $statusCode,
                    'message' => 'Erreur de validation',
                    'errors' => $decodedMessage
                ];
            } else {
                $data = [
                    'status' => $statusCode,
                    'message' => $message,
                ];
            }
        } elseif ($throwable instanceof NotEncodableValueException) {
            $statusCode = 400;
            $data = [
                'status' => $statusCode,
                'message' => 'Données JSON invalides',
            ];
        } elseif ($throwable instanceof ValidationFailedException) {
            $statusCode = 400;
            $violations = [];
            foreach ($throwable->getViolations() as $violation) {
                $violations[$violation->getPropertyPath()] = $violation->getMessage();
            }
            $data = [
                'status' => $statusCode,
                'message' => 'Erreur de validation',
                'errors' => $violations,
            ];
//            var_dump($data);
        } else {
            $message = $throwable->getMessage();
            $data = [
                'status' => $statusCode,
                'message' => $message ?: 'Une erreur est survenue'
            ];
        }
        // JsonResponse encodera automatiquement le tableau en JSON
        $response = new JsonResponse($data, $statusCode);
        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onExceptionEvent',
        ];
    }
}
