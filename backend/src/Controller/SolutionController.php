<?php

namespace App\Controller;

use App\Entity\Solution;
use App\Repository\SolutionRepository;
use App\Repository\ResourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/solutions', name: 'api_solutions_')]
class SolutionController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SolutionRepository $repository,
        private ResourceRepository $resourceRepository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $solutions = $this->repository->findAll();
        return $this->json($solutions);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $solution = $this->repository->find(Uuid::fromString($id));
        
        if (!$solution) {
            return $this->json(['error' => 'Solution not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($solution);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $solution = new Solution();
        $solution->setContent($data['content'] ?? null);
        
        if (isset($data['resourceId'])) {
            $resource = $this->resourceRepository->find(Uuid::fromString($data['resourceId']));
            if ($resource) {
                $solution->setResource($resource);
            }
        }

        $this->entityManager->persist($solution);
        $this->entityManager->flush();

        return $this->json($solution, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $solution = $this->repository->find(Uuid::fromString($id));
        
        if (!$solution) {
            return $this->json(['error' => 'Solution not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['content'])) {
            $solution->setContent($data['content']);
        }

        $this->entityManager->flush();

        return $this->json($solution);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $solution = $this->repository->find(Uuid::fromString($id));
        
        if (!$solution) {
            return $this->json(['error' => 'Solution not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($solution);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
