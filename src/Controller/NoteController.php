<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\View;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/notes')]
class NoteController extends AbstractController
{
    #[Route('/', name: 'app_note_all', methods: ['GET'])]
    public function all(NoteRepository $nr, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $nr->findBy(['is_public' => true], ['created_at' => 'DESC']),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('note/all.html.twig', [
            'allNotes' => $pagination,
        ]);
    }

    #[Route('/n/{slug}', name: 'app_note_show', methods: ['GET'])]
    public function show(string $slug, NoteRepository $nr, Request $request, EntityManagerInterface $em): Response
    {
        $note = $nr->findOneBySlug($slug);

        if (!$note) {
            throw $this->createNotFoundException('Note not found');
        }

        //---------- Ça finira dans un NoteViewService.php
        $view = new View();
        $view
        ->setNote($note)
        ->setIpAddress($request->getClientIp());
        $em->persist($view);
        $em->flush();
        //---------- Ça finira dans un NoteViewService.php

        return $this->render('note/show.html.twig', [
            'note' => $note,
            'creatorNotes' => $nr->findByCreator($note->getCreator()->getId()),
        ]);
    }

    #[Route('/u/{username}', name: 'app_note_user', methods: ['GET'])]
    public function userNotes(string $username, UserRepository $user,): Response
    {
        $creator = $user->findOneByUsername($username);

        if (!$creator) {
            throw $this->createNotFoundException('User not found');
        }

        return $this->render('note/user.html.twig', [
            'creator' => $creator,
            'userNotes' => $creator->getNotes(),
        ]);
    }

    #[Route('/new', name: 'app_note_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        if (!$this->getUser()) { // Si l'utilisateur n'est pas connecté
            $this->addFlash('error', 'You to need to be logged in to create a new note');
            return $this->redirectToRoute('app_login');
        }

        $note = new Note();
        $form = $this->createForm(NoteType::class, $note); // Chargement du formulaire
        $form = $form->handleRequest($request); // Recuperation des données de la requête POST

        // Traitement des données
        if ($form->isSubmitted() && $form->isValid()) {
            $note->setSlug($slugger->slug($note->getTitle()));
            $em->persist($note);
            $em->flush();

            $this->addFlash('success', 'Your note has been created');
            return $this->redirectToRoute('app_note_show', ['slug' => $note->getSlug()]);
        }
        return $this->render('note/new.html.twig', [
            'noteForm' => $form
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/edit/{slug}', name: 'app_note_edit', methods: ['GET', 'POST'])]
    public function edit(string $slug, NoteRepository $nr, Request $request, EntityManagerInterface $em): Response 
    {
        $note = $nr->findOneBySlug($slug);

        if ($note->getCreator() !== $this->getUser()) {
            $this->addFlash('error', 'You are not authorized to edit this note');
            return $this->redirectToRoute('app_note_show', ['slug' => $slug]);
        }

        $form = $this->createForm(NoteType::class, $note); // Chargement du formulaire avec les données de la note
        $form = $form->handleRequest($request); // Recuperation des données de la requête POST

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($note);
            $em->flush();

            $this->addFlash('success', 'Your note has been updated');
            return $this->redirectToRoute('app_note_show', ['slug' => $note->getSlug()]);
        }
        return $this->render('note/edit.html.twig', ['noteForm' => $form]);
    }

    #[Route('/delete/{slug}', name: 'app_note_delete', methods: ['POST'])]
    public function delete(string $slug, NoteRepository $nr, EntityManagerInterface $em): Response
    {
        $note = $nr->findOneBySlug($slug);

        $em->remove($note);
        $em->flush();

        $this->addFlash('success', 'Your code snippet has been deleted.');
        return $this->redirectToRoute('app_note_user');
    }
}
