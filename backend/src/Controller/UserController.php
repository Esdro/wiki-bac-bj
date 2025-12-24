<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/api/users', name: 'api_users_')]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $repository,
        private SerializerInterface $serializer,
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $users = $this->repository->findAll();
        if (count($users) < 1) {
            $users = [
                "hello" => "world"
            ];
        }
        return $this->json($users, 200, [], ['groups' => ['user:read']]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(User $user): JsonResponse
    {
        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($user, 200, [], ['groups' => ['user:read']]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json', [
            'groups' => ['user:write'],
        ]);
        
        if (isset($data['password'])) {
            $plainPassword = $data['password'];
            if (strlen($plainPassword) < 6) {
                return $this->json(['error' => 'Password must be at least 6 characters long'], Response::HTTP_BAD_REQUEST);
            }

            if (!preg_match('/[A-Z]/', $plainPassword) ||
                !preg_match('/[a-z]/', $plainPassword) ||
                !preg_match('/[0-9]/', $plainPassword) ) {
                return $this->json(['error' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number'], Response::HTTP_BAD_REQUEST);
            }

            // on vérifie si le mot de passe est déjà hashé (ce qui serait le cas s'il vient d'être créé)
            if ($this->passwordHasher->isPasswordValid($user, $plainPassword)) {
                return $this->json(['error' => 'Password is already hashed'], Response::HTTP_BAD_REQUEST);
            }

            $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json($user, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $user = $this->repository->find(Uuid::fromString($id));

        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['username'])) {
            $user->setUsername($data['username']);
        }
        if (isset($data['fullName'])) {
            $user->setFullName($data['fullName']);
        }
        if (isset($data['bio'])) {
            $user->setBio($data['bio']);
        }

        $this->entityManager->flush();

        return $this->json($user);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $user = $this->repository->find(Uuid::fromString($id));

        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }


    // login route 

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        if (!$username || !$password) {
            return $this->json(['error' => 'Username and password are required'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->repository->findOneBy(['username' => $username]);

        if (!$user) {
            return $this->json(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            return $this->json(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        // Generate a token or use JWT or any other method
        $token = bin2hex(random_bytes(16));

        // You should save the token in the database or cache with an expiration time

        $data = [
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
            ],
        ];

        return $this->json($data, Response::HTTP_OK);
    }


}
