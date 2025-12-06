<?php

namespace App\Controller;

use App\Entity\Exercise;
use App\Repository\ExerciseRepository;
use App\Repository\ResourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/exercises', name: 'api_exercises_')]
class ExerciseController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ExerciseRepository $repository,
        private ResourceRepository $resourceRepository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $exercises = $this->repository->findAll();
        return $this->json($exercises);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $exercise = $this->repository->find(Uuid::fromString($id));
        
        if (!$exercise) {
            return $this->json(['error' => 'Exercise not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($exercise);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $exercise = new Exercise();
        $exercise->setQuestion($data['question'] ?? null);
        $exercise->setAnswer($data['answer'] ?? null);
        $exercise->setDifficultyLevel($data['difficultyLevel'] ?? null);
        
        if (isset($data['resourceId'])) {
            $resource = $this->resourceRepository->find(Uuid::fromString($data['resourceId']));
            if ($resource) {
                $exercise->setResource($resource);
            }
        }

        $this->entityManager->persist($exercise);
        $this->entityManager->flush();

        return $this->json($exercise, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $exercise = $this->repository->find(Uuid::fromString($id));
        
        if (!$exercise) {
            return $this->json(['error' => 'Exercise not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['question'])) {
            $exercise->setQuestion($data['question']);
        }
        if (isset($data['answer'])) {
            $exercise->setAnswer($data['answer']);
        }
        if (isset($data['difficultyLevel'])) {
            $exercise->setDifficultyLevel($data['difficultyLevel']);
        }

        $this->entityManager->flush();

        return $this->json($exercise);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $exercise = $this->repository->find(Uuid::fromString($id));
        
        if (!$exercise) {
            return $this->json(['error' => 'Exercise not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($exercise);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
