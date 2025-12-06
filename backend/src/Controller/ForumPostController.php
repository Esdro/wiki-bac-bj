<?php

namespace App\Controller;

use App\Entity\ForumPost;
use App\Repository\ForumPostRepository;
use App\Repository\ForumTopicRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/forum-posts', name: 'api_forum_posts_')]
class ForumPostController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ForumPostRepository $repository,
        private ForumTopicRepository $topicRepository,
        private UserRepository $userRepository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $posts = $this->repository->findAll();
        return $this->json($posts, 200, [], ['groups' => ['forum:read']]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $post = $this->repository->find(Uuid::fromString($id));
        
        if (!$post) {
            return $this->json(['error' => 'Forum post not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($post, 200, [], ['groups' => ['forum:read']]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $post = new ForumPost();
        $post->setContent($data['content'] ?? null);
        
        if (isset($data['topicId'])) {
            $topic = $this->topicRepository->find(Uuid::fromString($data['topicId']));
            if ($topic) {
                $post->setTopic($topic);
            }
        }
        
        if (isset($data['authorId'])) {
            $author = $this->userRepository->find(Uuid::fromString($data['authorId']));
            if ($author) {
                $post->setAuthor($author);
            }
        }

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $this->json($post, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $post = $this->repository->find(Uuid::fromString($id));
        
        if (!$post) {
            return $this->json(['error' => 'Forum post not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['content'])) {
            $post->setContent($data['content']);
        }

        $this->entityManager->flush();

        return $this->json($post);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $post = $this->repository->find(Uuid::fromString($id));
        
        if (!$post) {
            return $this->json(['error' => 'Forum post not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($post);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
