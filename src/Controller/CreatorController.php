<?php

namespace App\Controller;

use App\Entity\Network;
use App\Form\CreatorType;
use App\Service\UploaderService;
use App\Repository\NoteRepository;
use App\Repository\NetworkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class CreatorController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', methods: ['GET'])]
    public function profile(): Response
    {
        return $this->render('creator/profile.html.twig', [
            'notes' => $this->getUser()->getNotes(),
        ]);
    }

    #[Route('/profile/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em, UploaderService $uploader): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(CreatorType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('image')->getData()) {
                if ($user->getImage()) {
                    $uploader->deleteImage($user->getImage());
                }
                $newFileName = $uploader->uploadImage($form->get('image')->getData());
                $user->setImage($newFileName);
            }
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Your profile has been updated');
            return $this->redirectToRoute('app_profile');
        }
        
        return $this->render('creator/edit.html.twig', [
            'creatorForm' => $form,
            'networks' => ['github', 'twitter', 'linkedin', 'facebook', 'reddit', 'instagram', 'youtube'],
        ]);
    }

    #[Route('/network', name: 'app_network', methods: ['POST'])]
    public function network(Request $request, EntityManagerInterface $em): Response
    {
        $networkName = $request->request->get('network_name');
        $networkUrl = $request->request->get('network_url') ?? false;
        
        if (!$networkName || !$networkUrl) {
            $this->addFlash('error', 'Please select a network and enter a url');
            return $this->redirectToRoute('app_profile_edit');
        }

        $network = new Network();
        $network
            ->setName($networkName)
            ->setUrl($networkUrl)
            ->setCreator($this->getUser());
        $em->persist($network);
        $em->flush();

        $this->addFlash('success', 'Your network has been added');
        return $this->redirectToRoute('app_profile');
    }

    #[Route('/network/delete', name: 'app_network_delete', methods: ['POST'])]
    public function deleteNetwork(Request $request, NetworkRepository $ntr, EntityManagerInterface $em): Response
    {
        $network = $ntr->find($request->request->get('network_id'));
        $em->remove($network);
        $em->flush();

        $this->addFlash('success', 'Your network has been deleted');
        return $this->redirectToRoute('app_profile');
    }
}
