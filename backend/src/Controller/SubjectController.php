<?php

namespace App\Controller;

use App\Entity\Subject;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/subjects', name: 'api_subjects_')]
class SubjectController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SubjectRepository $repository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $subjects = $this->repository->findAll();
        return $this->json($subjects, 200, [], ['groups' => ['subject:read']]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $subject = $this->repository->find(Uuid::fromString($id));
        
        if (!$subject) {
            return $this->json(['error' => 'Subject not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($subject, 200, [], ['groups' => ['subject:read']]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $subject = new Subject();
        $subject->setNameWithSlug($data['name'] ?? null);
        $subject->setCode($data['code'] ?? null);
        $subject->setIcon($data['icon'] ?? null);

        $this->entityManager->persist($subject);
        $this->entityManager->flush();

        return $this->json($subject, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $subject = $this->repository->find(Uuid::fromString($id));
        
        if (!$subject) {
            return $this->json(['error' => 'Subject not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['name'])) {
            $subject->setNameWithSlug($data['name']);
        }
        if (isset($data['code'])) {
            $subject->setCode($data['code']);
        }
        if (isset($data['icon'])) {
            $subject->setIcon($data['icon']);
        }

        $this->entityManager->flush();

        return $this->json($subject);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $subject = $this->repository->find(Uuid::fromString($id));
        
        if (!$subject) {
            return $this->json(['error' => 'Subject not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($subject);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
