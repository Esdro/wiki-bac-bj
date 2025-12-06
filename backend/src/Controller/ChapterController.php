<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Repository\ChapterRepository;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/chapters', name: 'api_chapters_')]
class ChapterController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ChapterRepository $repository,
        private SubjectRepository $subjectRepository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $chapters = $this->repository->findAll();
        return $this->json($chapters, 200, [], ['groups' => ['chapter:read']]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $chapter = $this->repository->find(Uuid::fromString($id));
        
        if (!$chapter) {
            return $this->json(['error' => 'Chapter not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($chapter, 200, [], ['groups' => ['chapter:read']]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $chapter = new Chapter();
        $chapter->setTitleWithSlug($data['title'] ?? null);
        $chapter->setOrderNum($data['orderNum'] ?? 0);
        $chapter->setDescription($data['description'] ?? null);
        
        if (isset($data['subjectId'])) {
            $subject = $this->subjectRepository->find(Uuid::fromString($data['subjectId']));
            if ($subject) {
                $chapter->setSubject($subject);
            }
        }

        $this->entityManager->persist($chapter);
        $this->entityManager->flush();

        return $this->json($chapter, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $chapter = $this->repository->find(Uuid::fromString($id));
        
        if (!$chapter) {
            return $this->json(['error' => 'Chapter not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['title'])) {
            $chapter->setTitleWithSlug($data['title']);
        }
        if (isset($data['orderNum'])) {
            $chapter->setOrderNum($data['orderNum']);
        }
        if (isset($data['description'])) {
            $chapter->setDescription($data['description']);
        }

        $this->entityManager->flush();

        return $this->json($chapter);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $chapter = $this->repository->find(Uuid::fromString($id));
        
        if (!$chapter) {
            return $this->json(['error' => 'Chapter not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($chapter);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
