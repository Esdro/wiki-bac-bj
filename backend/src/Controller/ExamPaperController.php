<?php

namespace App\Controller;

use App\Entity\ExamPaper;
use App\Repository\ExamPaperRepository;
use App\Repository\ResourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/exam-papers', name: 'api_exam_papers_')]
class ExamPaperController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ExamPaperRepository $repository,
        private ResourceRepository $resourceRepository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $examPapers = $this->repository->findAll();
        return $this->json($examPapers);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $examPaper = $this->repository->find(Uuid::fromString($id));
        
        if (!$examPaper) {
            return $this->json(['error' => 'Exam paper not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($examPaper);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $examPaper = new ExamPaper();
        $examPaper->setSession($data['session'] ?? null);
        $examPaper->setDuration($data['duration'] ?? null);
        
        if (isset($data['resourceId'])) {
            $resource = $this->resourceRepository->find(Uuid::fromString($data['resourceId']));
            if ($resource) {
                $examPaper->setResource($resource);
            }
        }

        $this->entityManager->persist($examPaper);
        $this->entityManager->flush();

        return $this->json($examPaper, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $examPaper = $this->repository->find(Uuid::fromString($id));
        
        if (!$examPaper) {
            return $this->json(['error' => 'Exam paper not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['session'])) {
            $examPaper->setSession($data['session']);
        }
        if (isset($data['duration'])) {
            $examPaper->setDuration($data['duration']);
        }

        $this->entityManager->flush();

        return $this->json($examPaper);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $examPaper = $this->repository->find(Uuid::fromString($id));
        
        if (!$examPaper) {
            return $this->json(['error' => 'Exam paper not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($examPaper);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
