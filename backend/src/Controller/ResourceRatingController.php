<?php

namespace App\Controller;

use App\Entity\ResourceRating;
use App\Repository\ResourceRatingRepository;
use App\Repository\ResourceRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/resource-ratings', name: 'api_resource_ratings_')]
class ResourceRatingController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ResourceRatingRepository $repository,
        private ResourceRepository $resourceRepository,
        private UserRepository $userRepository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $ratings = $this->repository->findAll();
        return $this->json($ratings);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $rating = $this->repository->find(Uuid::fromString($id));
        
        if (!$rating) {
            return $this->json(['error' => 'Resource rating not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($rating);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $rating = new ResourceRating();
        $rating->setRating($data['rating'] ?? null);
        $rating->setComment($data['comment'] ?? null);
        
        if (isset($data['resourceId'])) {
            $resource = $this->resourceRepository->find(Uuid::fromString($data['resourceId']));
            if ($resource) {
                $rating->setResource($resource);
            }
        }
        
        if (isset($data['userId'])) {
            $user = $this->userRepository->find(Uuid::fromString($data['userId']));
            if ($user) {
                $rating->setUser($user);
            }
        }

        $this->entityManager->persist($rating);
        $this->entityManager->flush();

        return $this->json($rating, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $rating = $this->repository->find(Uuid::fromString($id));
        
        if (!$rating) {
            return $this->json(['error' => 'Resource rating not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['rating'])) {
            $rating->setRating($data['rating']);
        }
        if (isset($data['comment'])) {
            $rating->setComment($data['comment']);
        }

        $this->entityManager->flush();

        return $this->json($rating);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $rating = $this->repository->find(Uuid::fromString($id));
        
        if (!$rating) {
            return $this->json(['error' => 'Resource rating not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($rating);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
