<?php

namespace App\Controller;

use App\Entity\ResourceTag;
use App\Repository\ResourceTagRepository;
use App\Repository\ResourceRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/resource-tags', name: 'api_resource_tags_')]
class ResourceTagController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ResourceTagRepository $repository,
        private ResourceRepository $resourceRepository,
        private TagRepository $tagRepository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $resourceTags = $this->repository->findAll();
        return $this->json($resourceTags);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $resourceTag = $this->repository->find(Uuid::fromString($id));
        
        if (!$resourceTag) {
            return $this->json(['error' => 'Resource tag not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($resourceTag);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $resourceTag = new ResourceTag();
        
        if (isset($data['resourceId'])) {
            $resource = $this->resourceRepository->find(Uuid::fromString($data['resourceId']));
            if ($resource) {
                $resourceTag->setResource($resource);
            }
        }
        
        if (isset($data['tagId'])) {
            $tag = $this->tagRepository->find(Uuid::fromString($data['tagId']));
            if ($tag) {
                $resourceTag->setTag($tag);
            }
        }

        $this->entityManager->persist($resourceTag);
        $this->entityManager->flush();

        return $this->json($resourceTag, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $resourceTag = $this->repository->find(Uuid::fromString($id));
        
        if (!$resourceTag) {
            return $this->json(['error' => 'Resource tag not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($resourceTag);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
