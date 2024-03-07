<?php

namespace App\Controller;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UsuarioController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $inputData = $request->toArray();
        // ... e.g. get the user data from a registration form
        $usuario = new Usuario($inputData['nip']);

        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $passwordHasher->hashPassword(
            $usuario,
            $inputData['senha']
        );
        $usuario->setPassword($hashedPassword);

        // Informa ao Doctrine que você deseja salvar esse novo objeto, quando for efetuado o flush.
        $this->entityManager->persist($usuario);

        // Efetua as alterações no banco de dados
        $this->entityManager->flush();

        return $this->json([
            'message'  => 'Usuário cadastrado com sucesso!',
            'id' => $usuario->getId(),
        ]);
    }

    #[Route('/hello', name: 'app_area_publica')]
    public function hello(): JsonResponse
    {
        return $this->json([
            'message' => 'Hello!'
        ]);
    }
}
