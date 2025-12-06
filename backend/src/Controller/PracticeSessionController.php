<?php

namespace App\Controller;

use App\Entity\PracticeSession;
use App\Repository\PracticeSessionRepository;
use App\Repository\UserRepository;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/practice-sessions', name: 'api_practice_sessions_')]
class PracticeSessionController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PracticeSessionRepository $repository,
        private UserRepository $userRepository,
        private SubjectRepository $subjectRepository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $sessions = $this->repository->findAll();
        return $this->json($sessions);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $session = $this->repository->find(Uuid::fromString($id));
        
        if (!$session) {
            return $this->json(['error' => 'Practice session not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($session);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $session = new PracticeSession();
        $session->setScore($data['score'] ?? null);
        $session->setTotalQuestions($data['totalQuestions'] ?? null);
        $session->setCorrectAnswers($data['correctAnswers'] ?? null);
        
        if (isset($data['userId'])) {
            $user = $this->userRepository->find(Uuid::fromString($data['userId']));
            if ($user) {
                $session->setUser($user);
            }
        }
        
        if (isset($data['subjectId'])) {
            $subject = $this->subjectRepository->find(Uuid::fromString($data['subjectId']));
            if ($subject) {
                $session->setSubject($subject);
            }
        }

        $this->entityManager->persist($session);
        $this->entityManager->flush();

        return $this->json($session, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $session = $this->repository->find(Uuid::fromString($id));
        
        if (!$session) {
            return $this->json(['error' => 'Practice session not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['score'])) {
            $session->setScore($data['score']);
        }
        if (isset($data['totalQuestions'])) {
            $session->setTotalQuestions($data['totalQuestions']);
        }
        if (isset($data['correctAnswers'])) {
            $session->setCorrectAnswers($data['correctAnswers']);
        }

        $this->entityManager->flush();

        return $this->json($session);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $session = $this->repository->find(Uuid::fromString($id));
        
        if (!$session) {
            return $this->json(['error' => 'Practice session not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($session);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
