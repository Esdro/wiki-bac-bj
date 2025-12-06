<?php

namespace App\Controller;

use App\Entity\Series;
use App\Repository\SeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/series', name: 'api_series_')]
class SeriesController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SeriesRepository $repository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $series = $this->repository->findAll();
        return $this->json($series);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $series = $this->repository->find(Uuid::fromString($id));
        
        if (!$series) {
            return $this->json(['error' => 'Series not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($series);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $series = new Series();
        $series->setNameWithSlug($data['name'] ?? null);
        $series->setCode($data['code'] ?? null);

        $this->entityManager->persist($series);
        $this->entityManager->flush();

        return $this->json($series, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $series = $this->repository->find(Uuid::fromString($id));
        
        if (!$series) {
            return $this->json(['error' => 'Series not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['name'])) {
            $series->setNameWithSlug($data['name']);
        }
        if (isset($data['code'])) {
            $series->setCode($data['code']);
        }

        $this->entityManager->flush();

        return $this->json($series);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $series = $this->repository->find(Uuid::fromString($id));
        
        if (!$series) {
            return $this->json(['error' => 'Series not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($series);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
