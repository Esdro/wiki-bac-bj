<?php

namespace App\Controller;

use App\Repository\RoleRepository;
use App\Repository\SubjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SubjectRepository $subjectRepository, RoleRepository $roleRepository, SerializerInterface $serializer): JsonResponse
    {
        $subjects = $subjectRepository->findAll();
        $roles = $roleRepository->findAll();

        $subjects = $serializer->serialize($subjects,  'json', ['groups' => ['subject:read']]);
        $roles = $serializer->serialize($roles, 'json', ['groups' => ['role:read']]);

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'data' => json_decode($subjects, true),
            'roles' => json_decode($roles, true)
        ], 200, []);
    }
}
