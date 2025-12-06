<?php

namespace App\Controller;

use App\Entity\RevisionSheet;
use App\Repository\RevisionSheetRepository;
use App\Repository\ResourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/revision-sheets', name: 'api_revision_sheets_')]
class RevisionSheetController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RevisionSheetRepository $repository,
        private ResourceRepository $resourceRepository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $sheets = $this->repository->findAll();
        return $this->json($sheets);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $sheet = $this->repository->find(Uuid::fromString($id));
        
        if (!$sheet) {
            return $this->json(['error' => 'Revision sheet not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($sheet);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $sheet = new RevisionSheet();
        $sheet->setContent($data['content'] ?? null);
        
        if (isset($data['resourceId'])) {
            $resource = $this->resourceRepository->find(Uuid::fromString($data['resourceId']));
            if ($resource) {
                $sheet->setResource($resource);
            }
        }

        $this->entityManager->persist($sheet);
        $this->entityManager->flush();

        return $this->json($sheet, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $sheet = $this->repository->find(Uuid::fromString($id));
        
        if (!$sheet) {
            return $this->json(['error' => 'Revision sheet not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['content'])) {
            $sheet->setContent($data['content']);
        }

        $this->entityManager->flush();

        return $this->json($sheet);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $sheet = $this->repository->find(Uuid::fromString($id));
        
        if (!$sheet) {
            return $this->json(['error' => 'Revision sheet not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($sheet);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
