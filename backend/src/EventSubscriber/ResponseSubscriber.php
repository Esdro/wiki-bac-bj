<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ResponseSubscriber implements EventSubscriberInterface
{
    public function onResponseEvent(ResponseEvent $event): void
    {
        // This method is called on every response


        $response = $event->getResponse();

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Cache-Control', 'store');
        $response->headers->set('X-Powered-By', 'Wikibac', true);
        $response->headers->set('Date', date('D, d M Y H:i:s'), true);
        // set encoding to utf-8
        $response->headers->set('Content-Encoding', 'utf-8');

        $status = $response->getStatusCode();
        $data = [];
        $content = json_decode($response->getContent());

        $contentAsArray = (array)$content;

        if (isset($contentAsArray[0]) && is_object(array_first($contentAsArray))) {
            $total = count($contentAsArray);
        } else {
            $total = 1;
        }
        if ($status == Response::HTTP_OK) {

            $data = [
                'status' => $status,
                'message' => 'Your request was successful ',
                'totalCount' => $total,
                'data' => $content,
            ];
        } elseif ($status == 201) {

            $data = [
                'status' => $status,
                'message' => 'created',
                'data' => $content,
            ];
        } elseif ($status == 202) {
            $data = [
                'status' => $status,
                'message' => 'accepted',
                'data' => $content,
            ];
        } elseif ($status == 204) {
            $data = [
                'status' => $status,
                'message' => 'no content',
            ];
        } else {
            $data = $contentAsArray;
        }

        $data = json_encode($data, JSON_PRETTY_PRINT);
        // var_dump($data);

        $response->setContent($data);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent::class => 'onResponseEvent',
        ];
    }
}
