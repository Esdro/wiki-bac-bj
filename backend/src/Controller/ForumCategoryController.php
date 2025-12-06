<?php

namespace App\Controller;

use App\Entity\ForumCategory;
use App\Repository\ForumCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/forum-categories', name: 'api_forum_categories_')]
class ForumCategoryController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ForumCategoryRepository $repository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $categories = $this->repository->findAll();
        return $this->json($categories);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $category = $this->repository->find(Uuid::fromString($id));
        
        if (!$category) {
            return $this->json(['error' => 'Forum category not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($category);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $category = new ForumCategory();
        $category->setNameWithSlug($data['name'] ?? null);
        $category->setDescription($data['description'] ?? null);

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $this->json($category, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $category = $this->repository->find(Uuid::fromString($id));
        
        if (!$category) {
            return $this->json(['error' => 'Forum category not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['name'])) {
            $category->setNameWithSlug($data['name']);
        }
        if (isset($data['description'])) {
            $category->setDescription($data['description']);
        }

        $this->entityManager->flush();

        return $this->json($category);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $category = $this->repository->find(Uuid::fromString($id));
        
        if (!$category) {
            return $this->json(['error' => 'Forum category not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
