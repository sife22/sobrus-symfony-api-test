<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasherInterface): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $new_user = new User();
        $new_user->setUsername($data['username']);
        $new_user->setEmail($data['email']);
        $new_user->setRoles(["ROLE_USER"]);
        $new_user->setPassword($userPasswordHasherInterface->hashPassword($new_user ,$data['password']));

        $entityManager->persist($new_user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User registered successfully!'], 201);
    }
}
