<?php

namespace App\Controller;

use App\Entity\SeriesSubject;
use App\Repository\SeriesSubjectRepository;
use App\Repository\SeriesRepository;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/series-subjects', name: 'api_series_subjects_')]
class SeriesSubjectController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SeriesSubjectRepository $repository,
        private SeriesRepository $seriesRepository,
        private SubjectRepository $subjectRepository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $seriesSubjects = $this->repository->findAll();
        return $this->json($seriesSubjects);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $seriesSubject = $this->repository->find(Uuid::fromString($id));
        
        if (!$seriesSubject) {
            return $this->json(['error' => 'Series subject not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($seriesSubject);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $seriesSubject = new SeriesSubject();
        $seriesSubject->setCoefficient($data['coefficient'] ?? null);
        
        if (isset($data['seriesId'])) {
            $series = $this->seriesRepository->find(Uuid::fromString($data['seriesId']));
            if ($series) {
                $seriesSubject->setSeries($series);
            }
        }
        
        if (isset($data['subjectId'])) {
            $subject = $this->subjectRepository->find(Uuid::fromString($data['subjectId']));
            if ($subject) {
                $seriesSubject->setSubject($subject);
            }
        }

        $this->entityManager->persist($seriesSubject);
        $this->entityManager->flush();

        return $this->json($seriesSubject, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $seriesSubject = $this->repository->find(Uuid::fromString($id));
        
        if (!$seriesSubject) {
            return $this->json(['error' => 'Series subject not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['coefficient'])) {
            $seriesSubject->setCoefficient($data['coefficient']);
        }

        $this->entityManager->flush();

        return $this->json($seriesSubject);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $seriesSubject = $this->repository->find(Uuid::fromString($id));
        
        if (!$seriesSubject) {
            return $this->json(['error' => 'Series subject not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($seriesSubject);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
