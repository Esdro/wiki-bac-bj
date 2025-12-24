<?php

namespace App\Controller;

use App\Entity\Resource;
use App\Repository\ResourceRepository;
use App\Repository\ResourceTypeRepository;
use App\Repository\SubjectRepository;
use App\Repository\ChapterRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/resources', name: 'api_resources_')]
class ResourceController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ResourceRepository     $repository,
        private readonly ResourceTypeRepository $typeRepository,
        private readonly UserRepository         $userRepository,
        private readonly SubjectRepository      $subjectRepository,
        private readonly ChapterRepository      $chapterRepository,
        private readonly SerializerInterface    $serializer,
        private readonly ValidatorInterface     $validator
    )
    {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $requestBody = (array)json_decode($request->getContent(), true);
        if (!empty($requestBody)) {
            $offset = $requestBody['offset'] ?? 0;
            $resources = $this->repository->findWithPagination(limit: 30, offset: $offset);
        } else {
            $resources = $this->repository->findAll();
        }
//        dd($resources);
        $data = $this->serializer->serialize($resources, 'json', ['groups' => ['resource:read']]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $resource = $this->repository->findOneBy(['id' => Uuid::fromString($id)]);
        if (!$resource) {
            return $this->json(['error' => 'Resource not found'], Response::HTTP_NOT_FOUND);
        }
        $data = $this->serializer->serialize($resource, 'json', ['groups' => ['resource:read']]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $resource = $this->serializer->deserialize($request->getContent(), Resource::class, 'json', ['groups' => ['resource:write']]);

        if (!($resource instanceof Resource)) {
//           var_dump("error");exit;
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Resource is not valid');
        }

        if (isset($data['userId'])) {
            $user = $this->userRepository->find(Uuid::fromString($data['userId']));
            if ($user) {
                $resource->setUser($user);
            }
        }
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

        $errors = $this->validator->validate($resource);

        if (count($errors) > 0) {
            throw new ValidationFailedException($resource, $errors);
        }

        $this->entityManager->persist($resource);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($resource, 'json', ['groups' => ['resource:read']]);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
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
    public function delete(Resource $resource): JsonResponse
    {
        $this->entityManager->remove($resource);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
