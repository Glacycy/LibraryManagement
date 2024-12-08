<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Borrow;
use App\Entity\User;
use App\Repository\BorrowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/app')]
class BorrowController extends AbstractController
{
    #[Route('/borrow', name: 'app_borrow')]
    public function index(): Response
    {
        return $this->render('borrow/index.html.twig', [

        ]);
    }

    #[Route('/borrow-list', name: 'app_borrow_list')]
    public function borrowList(BorrowRepository $borrowRepository): Response
    {
        $borrows = $borrowRepository->findAll();
        return $this->render('borrow/list.html.twig', [
            'borrows' => $borrows

        ]);
    }

    #[Route('/borrow/{id}', name: 'app_borrow_book')]
    public function borrowBook(
        Book $book,
        EntityManagerInterface $entityManager,
        #[CurrentUser] User $user = null
    ): Response
    {
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour emprunter un livre.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si le livre n'est pas déjà emprunté
        $existingBorrow = $entityManager->getRepository(Borrow::class)->findOneBy([
            'books' => $book,
            'status' => 'en cours'
        ]);

        if ($existingBorrow) {
            $this->addFlash('error', 'Ce livre est déjà emprunté.');
            return $this->redirectToRoute('app_book_show', ['id' => $book->getId()]);
        }

        // Créer un nouvel emprunt
        $borrow = new Borrow();
        $borrow->setBooks($book);
        $borrow->setUser($user);
        $borrow->setBorrowDate(new \DateTimeImmutable());
        $borrow->setStatus('en cours');

        $entityManager->persist($borrow);
        $entityManager->flush();

        $this->addFlash('success', 'Livre emprunté avec succès.');
        return $this->redirectToRoute('app_borrow_list');
    }

    #[Route('/borrow/{id}/return', name: 'app_borrow_return', methods: ['POST'])]
    public function returnBook(
        Borrow $borrow,
        EntityManagerInterface $entityManager
    ): Response
    {
        // Vérifie si l'utilisateur actuel est soit l'emprunteur, soit un admin
        if ($this->getUser() !== $borrow->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à retourner ce livre.');
            return $this->redirectToRoute('app_borrow_list');
        }

        // Vérifie si l'emprunt est déjà retourné
        if ($borrow->getStatus() === 'retourné') {
            $this->addFlash('error', 'Ce livre a déjà été retourné.');
            return $this->redirectToRoute('app_borrow_list');
        }

        // Met à jour le statut de l'emprunt
        $borrow->setStatus('retourné');
        $entityManager->flush();

        $this->addFlash('success', 'Livre retourné avec succès.');
        return $this->redirectToRoute('app_borrow_list');
    }

    #[Route('/borrow/{id}/delete', name: 'app_borrow_delete', methods: ['POST'])]
    public function deleteBorrow(
        Request $request,
        Borrow $borrow,
        EntityManagerInterface $entityManager
    ): Response
    {
        // Vérifie le token CSRF pour la sécurité
        if ($this->isCsrfTokenValid('delete'.$borrow->getId(), $request->request->get('_token'))) {
            // Seuls les admins peuvent supprimer un emprunt
            if (!$this->isGranted('ROLE_ADMIN')) {
                $this->addFlash('error', 'Vous n\'êtes pas autorisé à supprimer cet emprunt.');
                return $this->redirectToRoute('app_borrow_list');
            }

            $entityManager->remove($borrow);
            $entityManager->flush();

            $this->addFlash('success', 'L\'emprunt a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_borrow_list');
    }
}
