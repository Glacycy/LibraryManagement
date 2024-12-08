<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminUserController extends AbstractController
{
    #[Route('/users', name: 'admin_users_index')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin_user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/users/{id}/toggle-role', name: 'admin_users_toggle_role', methods: ['POST'])]
    public function toggleRole(User $user, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur essaie de modifier ses propres droits
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier vos propres droits d\'administration.');
            return $this->redirectToRoute('admin_users_index');
        }

        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles)) {
            $user->setRoles(['ROLE_USER']);
        } else {
            $user->setRoles(['ROLE_ADMIN']);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Les rôles ont été mis à jour avec succès.');
        return $this->redirectToRoute('admin_users_index');
    }
}