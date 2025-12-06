<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/roles', name: 'api_roles_')]
class RoleController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RoleRepository $repository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $roles = $this->repository->findAll();
        return $this->json($roles, 200, [], ['groups' => ['role:read']]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $role = $this->repository->find(Uuid::fromString($id));
        
        if (!$role) {
            return $this->json(['error' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($role, 200, [], ['groups' => ['role:read']]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $role = new Role();
        $role->setName($data['name'] ?? null);
        $role->setDescription($data['description'] ?? null);

        $this->entityManager->persist($role);
        $this->entityManager->flush();

        return $this->json($role, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $role = $this->repository->find(Uuid::fromString($id));
        
        if (!$role) {
            return $this->json(['error' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['name'])) {
            $role->setName($data['name']);
        }
        if (isset($data['description'])) {
            $role->setDescription($data['description']);
        }

        $this->entityManager->flush();

        return $this->json($role);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $role = $this->repository->find(Uuid::fromString($id));
        
        if (!$role) {
            return $this->json(['error' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($role);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
