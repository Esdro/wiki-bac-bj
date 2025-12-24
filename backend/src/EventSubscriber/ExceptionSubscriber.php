<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        $statusCode = 500;

        // convertir en tableau le trace pour l'afficher plus facilement
        $traceAsString = $throwable->getTraceAsString();
        $traceAsArray = explode("\n", $traceAsString);


        $data = [
            'status' => $statusCode,
            'message' => 'Une erreur est survenue',
            'error' => true,
            'details' => $traceAsArray,
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
                    'error' => true,
                    'details' => (array) $decodedMessage,
                ];
            } else {
                $data = [
                    'status' => $statusCode,
                    'error' => true,
                    'message' => $message,
                    'details' => $traceAsArray,

                ];
            }
        } elseif ($throwable instanceof NotEncodableValueException) {
            $statusCode = 400;
            $data = array_merge($data, [
                'status' => $statusCode,
                'message' => 'Données non encodables en JSON',
                'error' => true,
                'details' => $traceAsArray,
            ]);
        } elseif ($throwable instanceof ValidationFailedException) {
            $statusCode = 400;
            $violations = [];
            foreach ($throwable->getViolations() as $violation) {
                $violations[$violation->getPropertyPath()] = $violation->getMessage();
            }
            $data = array_merge($data, [
                'status' => $statusCode,
                'message' => 'Erreur de validation',
                'error' => true,
                'details' => (array) $violations,
            ]);
            //            var_dump($data);
        }else {
            $message = $throwable->getMessage();
            $data = array_merge($data, [
                'status' => $statusCode,
                'message' => $message ?: 'Une erreur est survenue',
                'error' => true,
                'details' => [],
            ]);
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
