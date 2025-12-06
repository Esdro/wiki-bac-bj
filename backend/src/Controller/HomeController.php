<?php

namespace App\Controller;

use App\Repository\RoleRepository;
use App\Repository\SubjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SubjectRepository $subjectRepository, RoleRepository $roleRepository): JsonResponse
    {
        $subjects = $subjectRepository->findAll();
        $roles = $roleRepository->findAll();

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'data' => $subjects,
            'roles' => $roles
        ], 200, [], ['groups' => ['subject:read', 'role:read']]);
    }
}
