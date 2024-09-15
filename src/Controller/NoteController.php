<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/notes')] // Suffixe pour les routes du controller
class NoteController extends AbstractController
{
    #[Route('/', name: 'app_note_all', methods: ['GET'])]
    public function all(NoteRepository $nr, PaginatorInterface $paginator, Request $request): Response
    {
        // Notes publiques
        $query = $nr->findBy(['is_public' => true], ['created_at' => 'DESC']);
        $notes = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('note/all.html.twig', [
            'notes' => $notes
        ]);
    }

    #[Route('/{slug}', name: 'app_note_show', methods: ['GET'])]
    public function show(string $slug, NoteRepository $nr): Response
    {
        $note = $nr->findOneBySlug($slug);
        if ($note === null) {
            throw $this->createNotFoundException('Note not found');
        } else {
            if ($note->isPublic()) {
                $creatorNotes = $nr->findByCreator($note->getCreator());
                return $this->render('note/show.html.twig', [
                    'note' => $note,
                    'creatorNotes' => $creatorNotes,
                ]);
            } else {
                throw $this->createAccessDeniedException('This note is private');
            }
        }
    }

    #[Route('/{username}', name: 'app_note_user', methods: ['GET'])]
    public function userNotes(
        string $username,
        UserRepository $user, // Cette fois on utilise le repository User
    ): Response {
        $creator = $user->findOneByUsername($username); // Recherche de l'utilisateur
        return $this->render('note/user.html.twig', [
            'creator' => $creator, // Envoie les données de l'utilisateur à la vue Twig
            'userNotes' => $creator->getNotes(), // Récupère les notes de l'utilisateur
        ]);
    }

    #[Route('/new', name: 'app_note_new', methods: ['GET', 'POST'])]
    public function new(): Response
    {
        // TODO: Formulaire de modification et traitement des données
        return $this->render('note/new.html.twig', [
            // TODO: Formulaire à envoyer à la vue Twig
        ]);
    }

    #[Route('/edit/{slug}', name: 'app_note_edit', methods: ['GET', 'POST'])]
    public function edit(string $slug, NoteRepository $nr): Response
    {
        $note = $nr->findOneBySlug($slug); // Recherche de la note à modifier
        // TODO: Formulaire de modification et traitement des données
        return $this->render('note/edit.html.twig', [
            // TODO: Formulaire à envoyer à la vue Twig
        ]);
    }

    #[Route('/delete/{slug}', name: 'app_note_delete', methods: ['POST'])]
    public function delete(string $slug, NoteRepository $nr): Response
    {
        $note = $nr->findOneBySlug($slug); // Recherche de la note à supprimer
        // TODO: Traitement de la suppression
        $this->addFlash('success', 'Your code snippet has been deleted.');
        return $this->redirectToRoute('app_note_user');
    }
}
