<?php

namespace App\Controller;

use App\Entity\Resource;
use App\Repository\ResourceRepository;
use App\Repository\ResourceTypeRepository;
use App\Repository\SubjectRepository;
use App\Repository\ChapterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/resources', name: 'api_resources_')]
class ResourceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ResourceRepository $repository,
        private ResourceTypeRepository $typeRepository,
        private SubjectRepository $subjectRepository,
        private ChapterRepository $chapterRepository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $resources = $this->repository->findAll();
        return $this->json($resources, 200, [], ['groups' => ['resource:read']]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $resource = $this->repository->find(Uuid::fromString($id));
        
        if (!$resource) {
            return $this->json(['error' => 'Resource not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($resource, 200, [], ['groups' => ['resource:read']]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $resource = new Resource();
        $resource->setTitleWithSlug($data['title'] ?? null);
        $resource->setDescription($data['description'] ?? null);
        $resource->setFileUrl($data['fileUrl'] ?? null);
        $resource->setYear($data['year'] ?? null);
        
        if (isset($data['typeId'])) {
            $type = $this->typeRepository->find(Uuid::fromString($data['typeId']));
            if ($type) {
                $resource->setType($type);
            }
        }
        
        if (isset($data['subjectId'])) {
            $subject = $this->subjectRepository->find(Uuid::fromString($data['subjectId']));
            if ($subject) {
                $resource->setSubject($subject);
            }
        }
        
        if (isset($data['chapterId'])) {
            $chapter = $this->chapterRepository->find(Uuid::fromString($data['chapterId']));
            if ($chapter) {
                $resource->setChapter($chapter);
            }
        }

        $this->entityManager->persist($resource);
        $this->entityManager->flush();

        return $this->json($resource, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $resource = $this->repository->find(Uuid::fromString($id));
        
        if (!$resource) {
            return $this->json(['error' => 'Resource not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['title'])) {
            $resource->setTitleWithSlug($data['title']);
        }
        if (isset($data['description'])) {
            $resource->setDescription($data['description']);
        }
        if (isset($data['fileUrl'])) {
            $resource->setFileUrl($data['fileUrl']);
        }
        if (isset($data['year'])) {
            $resource->setYear($data['year']);
        }

        $this->entityManager->flush();

        return $this->json($resource);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $resource = $this->repository->find(Uuid::fromString($id));
        
        if (!$resource) {
            return $this->json(['error' => 'Resource not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($resource);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
