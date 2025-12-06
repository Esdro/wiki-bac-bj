<?php

namespace App\Controller;

use App\Entity\UserProgress;
use App\Repository\UserProgressRepository;
use App\Repository\UserRepository;
use App\Repository\ChapterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/user-progress', name: 'api_user_progress_')]
class UserProgressController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserProgressRepository $repository,
        private UserRepository $userRepository,
        private ChapterRepository $chapterRepository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $progressEntries = $this->repository->findAll();
        return $this->json($progressEntries);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $progress = $this->repository->find(Uuid::fromString($id));
        
        if (!$progress) {
            return $this->json(['error' => 'User progress not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($progress);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $progress = new UserProgress();
        $progress->setStatus($data['status']);
        
        if (isset($data['userId'])) {
            $user = $this->userRepository->find(Uuid::fromString($data['userId']));
            if ($user) {
                $progress->setUser($user);
            }
        }
        
        if (isset($data['chapterId'])) {
            $chapter = $this->chapterRepository->find(Uuid::fromString($data['chapterId']));
            if ($chapter) {
                $progress->setChapter($chapter);
            }
        }

        $this->entityManager->persist($progress);
        $this->entityManager->flush();

        return $this->json($progress, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $progress = $this->repository->find(Uuid::fromString($id));
        
        if (!$progress) {
            return $this->json(['error' => 'User progress not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['isCompleted'])) {
            $progress->setIsCompleted($data['isCompleted']);
        }

        $this->entityManager->flush();

        return $this->json($progress);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $progress = $this->repository->find(Uuid::fromString($id));
        
        if (!$progress) {
            return $this->json(['error' => 'User progress not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($progress);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
