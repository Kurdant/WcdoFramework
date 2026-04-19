<?php

namespace App\Controller;

use App\Entity\Fonction;
use App\Form\FonctionType;
use App\Repository\FonctionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/fonction')]
class FonctionController extends AbstractController
{
    #[Route('', name: 'app_fonction_index', methods: ['GET'])]
    public function index(FonctionRepository $repo): Response
    {
        return $this->render('fonction/index.html.twig', [
            'fonctions' => $repo->findBy([], ['intitule' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_fonction_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $fonction = new Fonction();
        $form = $this->createForm(FonctionType::class, $fonction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($fonction);
            $em->flush();
            $this->addFlash('success', 'Fonction créée.');
            return $this->redirectToRoute('app_fonction_index');
        }

        return $this->render('fonction/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}/edit', name: 'app_fonction_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Fonction $fonction, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(FonctionType::class, $fonction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Fonction modifiée.');
            return $this->redirectToRoute('app_fonction_index');
        }

        return $this->render('fonction/edit.html.twig', ['form' => $form, 'fonction' => $fonction]);
    }
}
