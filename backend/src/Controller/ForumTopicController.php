<?php

namespace App\Controller;

use App\Entity\ForumTopic;
use App\Repository\ForumTopicRepository;
use App\Repository\ForumCategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/forum-topics', name: 'api_forum_topics_')]
class ForumTopicController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ForumTopicRepository $repository,
        private ForumCategoryRepository $categoryRepository,
        private UserRepository $userRepository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $topics = $this->repository->findAll();
        return $this->json($topics, 200, [], ['groups' => ['forum:read']]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $topic = $this->repository->find(Uuid::fromString($id));
        
        if (!$topic) {
            return $this->json(['error' => 'Forum topic not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($topic, 200, [], ['groups' => ['forum:read']]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $topic = new ForumTopic();
        $topic->setTitle($data['title'] ?? null);
        $topic->setContent($data['content'] ?? null);
        
        if (isset($data['categoryId'])) {
            $category = $this->categoryRepository->find(Uuid::fromString($data['categoryId']));
            if ($category) {
                $topic->setCategory($category);
            }
        }
        
        if (isset($data['authorId'])) {
            $author = $this->userRepository->find(Uuid::fromString($data['authorId']));
            if ($author) {
                $topic->setAuthor($author);
            }
        }

        $this->entityManager->persist($topic);
        $this->entityManager->flush();

        return $this->json($topic, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $topic = $this->repository->find(Uuid::fromString($id));
        
        if (!$topic) {
            return $this->json(['error' => 'Forum topic not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['title'])) {
            $topic->setTitle($data['title']);
        }
        if (isset($data['content'])) {
            $topic->setContent($data['content']);
        }

        $this->entityManager->flush();

        return $this->json($topic);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $topic = $this->repository->find(Uuid::fromString($id));
        
        if (!$topic) {
            return $this->json(['error' => 'Forum topic not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($topic);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
