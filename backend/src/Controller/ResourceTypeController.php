<?php

namespace App\Controller;

use App\Entity\ResourceType;
use App\Repository\ResourceTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/resource-types', name: 'api_resource_types_')]
class ResourceTypeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ResourceTypeRepository $repository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $types = $this->repository->findAll();
        return $this->json($types, 200, [], ['groups' => ['resource_type:read']]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $type = $this->repository->find(Uuid::fromString($id));
        
        if (!$type) {
            return $this->json(['error' => 'Resource type not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($type, 200, [], ['groups' => ['resource_type:read']]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $type = new ResourceType();
        $type->setNameWithSlug($data['name'] ?? null);
        $type->setDescription($data['description'] ?? null);

        $this->entityManager->persist($type);
        $this->entityManager->flush();

        return $this->json($type, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $type = $this->repository->find(Uuid::fromString($id));
        
        if (!$type) {
            return $this->json(['error' => 'Resource type not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['name'])) {
            $type->setNameWithSlug($data['name']);
        }
        if (isset($data['description'])) {
            $type->setDescription($data['description']);
        }

        $this->entityManager->flush();

        return $this->json($type);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $type = $this->repository->find(Uuid::fromString($id));
        
        if (!$type) {
            return $this->json(['error' => 'Resource type not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($type);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
